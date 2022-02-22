<?php

namespace LimeSurveyProfessional\notifications;

use LimeSurveyProfessional\DataTransferObject;
use LimeSurveyProfessional\LinksAndContactHmtlHelper;

class GracePeriodNotification
{

    /**
     * days after which grace period starts
     */
    const GRACE_PERIOD_MIN = 15;

    /**
     * days after which grace period ends
     */
    const GRACE_PERIOD_MAX = 30;

    /** @var \LimeSurveyProfessional */
    public $plugin;

    /**
     * Constructor for LimitReminderNotification
     *
     * $plugin needs to be available here for plugin translation function
     *
     * @param \LimeSurveyProfessional $plugin
     */
    public function __construct(\LimeSurveyProfessional $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * creates notification for "in grace period", if required
     * @param DataTransferObject $dto
     *
     * @return void
     * @throws \CException
     */
    public function createNotification(DataTransferObject $dto)
    {
        if ($this->isInGracePeriod(new \DateTime(), $dto)) {
            $links = new LinksAndContactHmtlHelper();
            $not = new \UniqueNotification(array(
                'user_id' => App()->user->id,
                'importance' => \Notification::HIGH_IMPORTANCE,
                'title' => $this->getTitle(),
                'message' => $this->getMessage($links, $dto->isSiteAdminUser) . $this->getButton($links, $dto->isSiteAdminUser)
            ));
            $not->save();
        }
    }

    /**
     * Calculates if user is in grace period.
     * @param \DateTime $now For testing purposes this a function parameter for the current date
     * @param DataTransferObject $dto
     *
     * @return boolean  true, if user is grace period, false otherwise
     * @throws \CException
     */
    public function isInGracePeriod(\DateTime $now, DataTransferObject $dto)
    {
        $subscriptionCreated = new \DateTime($dto->dateSubscriptionCreated);
        $subscriptionPaidIsSet = $dto->dateSubscriptionPaid !== null && $dto->dateSubscriptionPaid != '';
        $subscriptionPaid = new \DateTime($dto->dateSubscriptionPaid);
        $latestBillingDate = $this->getLastPaymentDueDate($subscriptionCreated, $now, $dto->paymentPeriod);

        if ($subscriptionPaidIsSet && $latestBillingDate <= $subscriptionPaid) {
//          subscription is paid: all good
            return false;
        }
        $dateDiff = $latestBillingDate->diff($now);
        $differenceDays = (int)$dateDiff->format('%r%a');

        return ($differenceDays >= self::GRACE_PERIOD_MIN) && ($differenceDays <= self::GRACE_PERIOD_MAX);
    }

    /**
     * Returns the last payment due date on the basis of the subscription_created date.
     * There are 60 minutes subtracted because sometimes subscription_paid was few seconds too early.
     *
     * @param \DateTime $subscriptionCreated
     * @param \DateTime $now For testing purposes this a function parameter for the current date
     * @param string $paymentPeriod
     *
     * @return \DateTime
     * @throws \CException
     */
    private function getLastPaymentDueDate(\DateTime $subscriptionCreated, \DateTime $now, string $paymentPeriod)
    {
        $lastDueDate = $subscriptionCreated;

        if ($subscriptionCreated < $now) {
            if ($paymentPeriod === 'M') { // Monthly
                $lastDueDate = $this->getSimulatedNbillDay($subscriptionCreated, $now);
            } else { // Yearly
                $dateDiff = $subscriptionCreated->diff($now);
                $numberOfYears = (int)$dateDiff->format('%y');
                $lastDueDate->add(new \DateInterval('P' . $numberOfYears . 'Y'));
            }
        }
        // 60 minutes graceperiod because sometimes subscription_paid comes in seconds earlier than subscription_created
        $lastDueDate->sub(new \DateInterval('PT60M'));

        return $lastDueDate;
    }

    /**
     * Returns date of the last due date calculated the way nbill does it when monthly subscription.
     * bill functions behaviour is a bit strange:
     *
     * because it seems to put invalid dates into the mktime function:
     * example 1: if last_due_date is 2021-03-31 - the next_due_date would be 2021-04-31 which doesn't exist.
     *            mktime function would return a timestamp for 2021-05-01.
     *            So every future due_date would be the 1st of a month.
     * example 2: if last_due_date is 2021-01-31 - the next_due_date would be 2021-02-31 which doesn't exist.
     *             mktime function would return a timestamp for 2021-03-03
     *              So every future due_date would be the 3rd of a month.
     *
     * We need to simulate that process in a loop for each month since creation of subscription until now
     * as long the day number is above 28.
     *
     * @param \DateTime $subscriptionCreated
     * @param \DateTime $now
     * @return \DateTime
     */
    private function getSimulatedNbillDay(\DateTime $subscriptionCreated, \DateTime $now)
    {
        $nextDueDate = clone $subscriptionCreated;
        $loopDueDate = clone $nextDueDate;
        $day = (int)$loopDueDate->format('d');

        while ($now >= $nextDueDate && $day > 28) {
            $nextDueDateTimestamp = $this->getNextPaymentDateNbill(
                $subscriptionCreated->getTimestamp(),
                $loopDueDate->getTimestamp()
            );
            $loopDueDate->setTimestamp($nextDueDateTimestamp);

            if ($loopDueDate <= $now) {
                $nextDueDate = clone $loopDueDate;
                $day = (int)$loopDueDate->format('d');
            } else {
                // in case of the $nextDueDate was never updated in this loop, we need to break it here
                // to be on the safe side
                break;
            }
        }
        // the loop only handles dates with day number above 28. so the rest of the work is done here:
        $todayDay = (int)$now->format('d');
        $lastDueDateDay = (int)$nextDueDate->format('d');
        $lastDueDateMonth = (int)$now->format('m');
        $lastDueDateYear = (int)$now->format('Y');


        if ($todayDay < $lastDueDateDay) { // last due-date is in previous month
            $lastDueDateMonth = $lastDueDateMonth == 1 ? 12 : $lastDueDateMonth - 1;
            if ($lastDueDateMonth == 12) {
                $lastDueDateYear--; // last due-date is also in previous year
            }
        }

        $lastDueDate = new \DateTime();
        $lastDueDate->setTimestamp(
            mktime(
                (int)$nextDueDate->format('G'),
                (int)$nextDueDate->format('i'),
                (int)$nextDueDate->format('s'),
                $lastDueDateMonth,
                $lastDueDateDay,
                $lastDueDateYear
            )
        );

        return $lastDueDate;
    }

    /**
     * Copied from nbill - slighlty reduced and adjusted for what we need here
     *
     *
     * @param int $first $order->start_date timestamp
     * @param int $start $last_due_date timestamp
     * @return int
     */
    private function getNextPaymentDateNbill(int $first, int $start)
    {
        //For updating last and next payment date
        $leapYear = false;
        $firstDateParts = getdate($first);
        $firstDateMday = $firstDateParts['mday']; //If this is > 28, may need to adjust result (eg. if first payment was on 30th Jan, next will be 28th Feb (or 1st March), after that we need to revert to 30th (March) - not 28th again (nor 1st April).
        $startDateParts = getdate($start);
        $startDateYear = $startDateParts['year'];
        $lastDayFebAsInt = mktime(0, 0, 0, 2, 28, $startDateYear);
        $leapYearDate = getdate($lastDayFebAsInt + (24 * 60 * 60));
        $startDateMday = $startDateParts['mday'];
        if ($leapYearDate['mon'] == 2) {
            $leapYear = true;
        }
        if ($startDateMday == 28 && $firstDateMday > 28 ||
            $startDateMday == 29 && $firstDateMday > 29 ||
            $startDateMday == 30 && $firstDateMday > 30) {
            //Start date day is artifically low (due to month of previous payment cycle having fewer days that the first month)
            switch ($firstDateMday) {
                case 29:
                    switch ($startDateParts['mon']) {
                        case 2:
                            if ($leapYear) {
                                $startDateMday = 29;
                            } else {
                                $startDateMday = 28;
                            }
                            break;
                        default:
                            $startDateMday = 29;
                            break;
                    }
                case 30:
                case 31:
                    switch ($startDateParts['mon']) {
                        case 1:
                        case 3:
                        case 5:
                        case 7:
                        case 8:
                        case 10:
                        case 12:
                            $startDateMday = $firstDateMday;
                            break;
                        case 2:
                            if ($leapYear) {
                                $startDateMday = 29;
                            } else {
                                $startDateMday = 28;
                            }
                            break;
                        default:
                            $startDateMday = 30;
                            break;
                    }
            }
        }

        if ($startDateParts["mon"] == 12) {
            $new_month = 1;
            $new_year = $startDateYear + 1;
        } else {
            $new_month = $startDateParts["mon"] + 1;
            $new_year = $startDateYear;
        }
        $nextDueDate = mktime(
            $startDateParts["hours"],
            $startDateParts["minutes"],
            $startDateParts["seconds"],
            $new_month,
            $startDateMday,
            $new_year
        );

        //If first date was 29th or later, and next_due_date comes out as 1st March, set to 28th Feb (or 29th if leap year)
        $nextDueDateParts = getdate($nextDueDate);
        if ($firstDateMday > 28 && ($nextDueDateParts['mday'] <= $startDateMday - 28) && $nextDueDateParts['mon'] == 3) //if ($firstDateMday > 28 && $nextDueDateParts['mday'] == 1 && $nextDueDateParts['mon'] == 3)
        {
            if ($leapYear) {
                $nextDueDate = mktime(
                    $nextDueDateParts['hours'],
                    $nextDueDateParts['minutes'],
                    $nextDueDateParts['seconds'],
                    2,
                    29,
                    $nextDueDateParts['year']
                );
            } else {
                $nextDueDate = mktime(
                    $nextDueDateParts['hours'],
                    $nextDueDateParts['minutes'],
                    $nextDueDateParts['seconds'],
                    2,
                    28,
                    $nextDueDateParts['year']
                );
            }
        }

        if ($nextDueDate > 0) {
            $nextDueDate = mktime(
                0,
                0,
                0,
                $nextDueDateParts['mon'],
                $nextDueDateParts['mday'],
                $nextDueDateParts['year']
            );
        }

        return $nextDueDate;
    }

    /**
     * Generates and returns the title of the notification
     * @return string
     */
    private function getTitle()
    {
        return $this->plugin->gT('Unpaid invoice');
    }

    /**
     * Generates and returns the html formatted message of the notification
     * @param LinksAndContactHmtlHelper $links
     * @param bool $isSiteAdminUser
     *
     * @return string
     */
    private function getMessage(LinksAndContactHmtlHelper $links, bool $isSiteAdminUser)
    {
        if ($isSiteAdminUser) {
            $message = sprintf(
                $this->plugin->gT(
                    'You have an unpaid invoice. To avoid being locked out of your survey site, please view and pay the invoice from the %s tab of your LimeSurvey account homepage'
                ),
                $links->toHtmlLink(
                    $links->getTransactionHistoryLink(\Yii::app()->session['adminlang']),
                    $this->plugin->gT('transaction history')
                )
            );
        } else {
            $message = sprintf(
                $this->plugin->gT(
                    'There is an unpaid invoice for this account. Please contact your survey site admin, %s, to have the invoice paid to avoid being locked out of your survey site.'
                ),
                $links->toHtmlMailLink($links->getSiteAdminEmail())
            );
        }
        return '<p>' . $message . '</p>';
    }

    /**
     * Generates and returns the button html
     * @param LinksAndContactHmtlHelper $links
     * @param bool $isSiteAdminUser
     *
     * @return string
     */
    private function getButton(LinksAndContactHmtlHelper $links, bool $isSiteAdminUser)
    {
        if ($isSiteAdminUser) {
            $button = $links->toHtmlLinkButton(
                $links->getTransactionHistoryLink(\Yii::app()->session['adminlang']),
                $this->plugin->gT('Pay invoice')
            );
        } else {
            $button = $links->toHtmlMailLinkButton(
                $links->getSiteAdminEmail(),
                $this->plugin->gT('Contact Survey Site Adminstrator')
            );
        }

        return '<br><br><p class="text-center">' . $button . '</p>';
    }
}
