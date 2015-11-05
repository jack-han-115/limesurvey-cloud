REM Convert .mo to po 
REM for /R %%f in (*.mo) do msgunfmt "%%f" > "%%~pf%%~nf.po"
REM Update po from pot
for /R %%f in (*.po) do msgmerge --update "%%f" "C:\xampp\htdocstemp\limeservice.git\limesurvey\dbversion-177\locale\_template\limesurvey.pot"