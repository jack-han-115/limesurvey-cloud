<?php

/**
 * PDF Notification Copyright by Kai Ravesloot
 * User: kairavesloot
 * Date: 26.03.14
 * Time: 19:32
 *
 */
class PDFNotify_license {
  private $key;


  function __construct($key) {
    // Use Default Binary Key or generate yours
    $this->key = $key;
  }

  /**
   * first step to validate string
   * @return bool
   */

  public function check_key() {
  $myKey = trim($this->key);

$data =array(
'D3740-A2226-58BD1-F2C85-44694',
'A4492-DF229-01F2E-419C0-F646B',
'4B272-8D3F0-54F63-5341F-26408',
'3AE72-E5FDC-ECF71-9B334-BF4B9',
'8D0AB-B92C8-82BC9-28B78-76F62',
'128D1-4FDA4-FEA54-388B6-E45D8',
'E535F-8F7C1-4A557-25BD3-F82CE',
'B2A91-D5F2E-C0C8D-44168-2DEE0',
'DF332-F9312-1ACCC-C0858-6AA75',
'2D172-10612-2773C-0E9BE-F13F7',
'E39C1-C9CFE-BA470-C9AA9-63582',
'84D61-823D8-639AC-D9073-1BEEC',
'A639E-CC8D1-9F627-8C7C6-9D5C9',
'BC199-76F4F-C7553-43447-17EFE',
'E22E5-8A5F1-176AB-64348-25873',
'16DD1-D00D9-5AF45-79154-3B91A',
'567AC-D954C-C1658-32D21-07A50',
'C27C0-7AFB4-B6C29-A383B-60623',
'932D3-D6D01-39020-67362-6ECD3',
'CFA89-E4F81-46956-E425B-DE348',
'63CEB-A42DE-D8CDB-69C27-FD32F',
'A5E11-B4E99-549F5-9E18E-04B0B',
'21897-0B56A-1D391-C93E0-94D3A',
'3530-03B71-B1A9B-BE801-B9B3D',
'7878A-946F0-F7843-22116-936F2',
'DBC4F-0281B-EF8D6-9D537-E6D0C',
'05876-B62E5-DF59B-88EA3-558F4',
'71493-23DFE-1D1A2-D1C73-644F1',
'6DE23-E85C0-249DB-AA465-312B8',
'89730-A8A96-A4ABF-B0C35-C6CB3',
'6C068-54459-8A42E-1103F-6FC75',
'B7C51-71125-4B7E3-56E55-2D73D',
'09F09-761B6-4F3B2-4B6B1-ABB7E',
'2484D-7C20D-54512-23988-C419B',
'59DB8-E2E77-B3ABD-01EB9-EA68A',
'96BF7-7D838-735F8-AFDEB-A0C2C',
'BD7DD-BC421-85E57-9BB09-36B71',
'65E56-F1C63-473E1-4ADFE-B59D5',
'92A2F-CF638-F1BFD-B1E99-EA3F8',
'9F5F7-958AE-CD0BE-5E681-A403F',
'93C73-6AD1F-D6C16-70923-41A1C',
'176B3-4C5A0-D8ECB-F9D3F-12BA6',
'8B9FC-4400E-65AFB-15627-68D49',
'D7C46-BCD63-967B6-2DA72-93710',
'54F8B-AE043-46ADE-4C9FA-9E623',
'79B7D-6D5A3-CDC27-079C8-E0111',
'AEBE9-CB312-99743-12F0A-794CD',
'36814-6C4F7-458B3-2E4D1-6D0E1',
'75553-787B9-72BC2-3A6F9-A0E8D',
'ED525-E3129-7299F-CF3D8-BAFED',
'14765-62083-A79F4-5CA08-45FD9',
'7B4D1-0C499-9102B-82356-1DB89',
'CBE2B-38138-982D6-026B2-FC988',
'43B48-40ABE-4E36F-94178-BC057',
'E2C1C-C379E-ACB5F-38115-DEA3B',
'7F795-F512D-BEEC2-FB89A-16402',
'F54FC-FBFCD-93CC7-47485-8155D',
'5ACD1-B1574-64641-A799F-F66DC',
'68C5D-62DB3-46A9A-E92E1-0B02C',
'1EBF9-47B1A-F283A-27EEC-95215',
'D8DA1-D1050-C34C5-D10B1-216DD',
'23BC4-4D58C-588EB-CC35C-9059F',
'AFB6D-DB5D1-68705-F8499-DBCDB',
'5827A-D3CDC-21883-4CFA0-9DBC4',
'A163B-76081-30F26-A5398-8B42B',
'C96B7-F5A70-61FC2-38DC4-5A9D7',
'3D0F4-D806C-17F20-998AB-FCAD7',
'1DAC0-CFDE8-C1622-C6060-DE4C0',
'1642A-D0BA9-7A0CF-76955-27248',
'E1AC8-8B1AA-6DD08-FA083-4F62E',
'7EF90-29D50-90C5F-F8597-C1476',
'CE474-062DF-B837A-B5C9C-CD69A',
'4C2E1-87143-2512A-ADD67-E3942',
'D5E42-941D1-3F269-DF37D-92552',
'896FD-79833-3BCF4-846AE-47A1E',
'CC706-BAFAF-A04FF-086C3-6B5A4',
'2E073-9D790-EF7DE-E8F32-4F3F0',
'25C7B-1C9CD-FF619-BB1E7-F0258',
'BCF07-3FEF8-1EE8A-245B6-243A8',
'12077-9E32B-B204A-B958D-AE30A',
'409CB-ECF81-FBEF8-ADAF8-8D953',
'AD0A0-6F2DE-AE7C9-3FC82-D2D9B',
'B07FB-CC601-3B956-DFB89-592E7',
'3CAE3-7D079-2A2DC-477EE-69F13',
'0C9B1-06710-31B35-60EBB-B7986',
'4141D-EEFCF-34E80-AB92B-9D97E',
'6E33F-7F55D-9267C-89D9C-482BE',
'4F4F3-D4A02-609AB-AE155-309D4',
'BE1BD-4C888-622D7-86345-1E6E9',
'6AA91-993B9-1842C-F846E-ECB08',
'B16AE-098CE-A9CB0-F4857-5E4B5',
'E4EFA-24B64-D7630-56D4B-A7703',
'02F0B-0CFB6-C29F6-EC12A-5C722',
'E56BD-2EC4A-B1886-13D12-0598D',
'8872B-20545-09DF8-C751B-3A481',
'5E8D6-D9E12-895EA-BBBEF-36B22',
'28F21-34C35-BE9D4-28735-C9C1D',
'B6440-8E9AE-C8B4B-FACAA-DE123',
'E0B2E-1B0C4-AEE25-39E03-801AC',
'DEE98-B554C-0D6DA-96FC1-4EDB5');

    if (!empty($myKey)) {
       $data =  array_map('trim', $data);
      if(in_array($myKey, $data , True)){
        return true;
      }
    }
  }


}

