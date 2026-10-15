<?php

namespace App\Services;

use \Exception;
use \DOMDocument;
use \DOMXPath;
use \DateTime;
use \DateTimeZone;

//Nota Fiscal de Consumidor Eletrônica Parser

class NFCE {

    private ?string $Name =  null;
    private ?string $CNPJ =  null;
    private ?string $Address =  null;
    private ?float $Taxes =  null;
    private ?array $Items =  null;
    private ?string $IssueDate = null;
    private ?int $IssueDateTimestamp = null;

    public function __construct(string $Path) {

        if(empty($Path))
            throw new Exception('The path cannot be empty!');

        $Content = $this->fetchContent($Path);

        if(empty($Content))
            throw new Exception('The content cannot be empty!');

        $Result = $this->parseContent($Content);

        if(empty($Result)){

            foreach(get_object_vars($this) as $Index => $Value){

                if(is_null($Value))
                    throw new Exception("NFCE parse issue, empty $Index!");

            }

        }

    }

    private function getUserAgent() : string {

        return 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36';

    }

    private function fetchContent(string $Path) : string {

        $Options = [ 'http' => ['ignore_errors' => true, 'timeout' => 10, 'user_agent' => $this->getUserAgent(),'method'  => 'GET']];
        $Content = @file_get_contents($Path, false, stream_context_create($Options));

        if(empty($Content))
            return '';

        return $Content;

    }

    private function parseContent(string $Page) : bool {

        $Document = new DOMDocument();
        $Page = mb_convert_encoding($Page, 'HTML-ENTITIES', 'UTF-8');
        @$Document->loadHTML($Page);
        $XPath = new DOMXPath($Document);

        $Content = $XPath->query("//div[contains(@id, 'conteudo')]");

        if(empty($Content->length))
            return false;

        $Content = $Content->item(0);

        $Topo = $XPath->query(".//div[contains(@class, 'txtTopo')]", $Content);

        if(empty($Topo->length))
            return false;

        $Topo = $Topo->item(0);

        $Name = trim($Topo->nodeValue);

        $this->Name = $Name;

        $Text = $XPath->query(".//div[contains(@class, 'text')]", $Content);

        if($Text->length != 2)
            return false;

        $CNPJ = $Text->item(0)->nodeValue;
        $CNPJ = str_replace('CNPJ:','',$CNPJ);
        $CNPJ = trim($CNPJ);

        $this->CNPJ = $CNPJ;
        unset($CNPJ);

        $Address = trim($Text->item(1)->nodeValue);
        $Tokens = explode(',', $Address);

        if(count($Tokens) == 1)
            return false;

        foreach($Tokens as $Index => $Token){

            $Tokens[$Index] = trim($Token);

            if(empty($Tokens[$Index]))
                unset($Tokens[$Index]);

        }

        $this->Address = implode(',', $Tokens);

        $Table = $XPath->query("//table[contains(@class, 'filterable')]");

        if(empty($Table->length))
            return false;

        $Table = $Table->item(0);

        $TableRows = $XPath->query(".//tr", $Table);

        $Taxes = $XPath->query(".//div[contains(@class, 'spcTop')]", $Content);

        if(empty($Taxes->length))
            return false;

        $Taxes = $Taxes->item(0);

        $Taxes = $XPath->query(".//span[contains(@class, 'totalNumb')]", $Taxes);

        if(empty($Taxes->length))
            return false;

        $Taxes = trim($Taxes->item(0)->nodeValue);
        $Taxes = str_replace(',','.',$Taxes);
        $this->Taxes = (float) $Taxes;

        $Lines = [];

        foreach($TableRows as $Index => $Row){

            $TableDataLines = $XPath->query(".//td", $Row);

            if(empty($TableDataLines->length))
                return false;

            $TableDataLines = $TableDataLines->item(0);

            $Name = $XPath->query(".//span[contains(@class, 'txtTit')]", $TableDataLines);

            if(empty($Name->length))
                return false;

            $Name = trim($Name->item(0)->nodeValue);

            $Cod = $XPath->query(".//span[contains(@class, 'RCod')]", $TableDataLines);

            if(empty($Cod->length))
                return false;

            $Cod = trim($Cod->item(0)->nodeValue);
            $Cod = str_replace('(Código:','',$Cod);
            $Cod = str_replace(')','',$Cod);
            $Cod = trim($Cod);

            $Qtd = $XPath->query(".//span[contains(@class, 'Rqtd')]", $TableDataLines);

            if(empty($Qtd->length))
                return false;

            $Qtd = trim($Qtd->item(0)->nodeValue);
            $Qtd = str_replace('Qtde.:','',$Qtd);
            $Qtd = str_replace(',','.',$Qtd);
            $Qtd = (float) $Qtd;

            $Unit = $XPath->query(".//span[contains(@class, 'RUN')]", $TableDataLines);

            if(empty($Unit->length))
                return false;

            $Unit = trim($Unit->item(0)->nodeValue);
            $Unit = str_replace('UN:','',$Unit);
            $Unit = trim($Unit);

            $UnitPrice = $XPath->query(".//span[contains(@class, 'RvlUnit')]", $TableDataLines);

            if(empty($UnitPrice->length))
                return false;

            $UnitPrice = trim($UnitPrice->item(0)->nodeValue);
            $UnitPrice = str_replace('Vl. Unit.:','',$UnitPrice);
            $UnitPrice = trim($UnitPrice);
            $UnitPrice = preg_replace('/[\s\x{00A0}]+/u', '', $UnitPrice);
            $UnitPrice = str_replace(',','.',$UnitPrice);
            $UnitPrice = (float) $UnitPrice;

            $Lines[$Index] = ['Name'=> $Name,'Cod' => $Cod,'Qtd' => $Qtd,'Unit' => $Unit,'UnitPrice' => $UnitPrice,'TotalPrice' => $Qtd*$UnitPrice];

        }

        $this->Items = $Lines;

        $Details = $XPath->query("//div[contains(@class, 'jqm-collapsible')]");

        if(empty($Details->length))
            return false;

        $Details = $Details->item(0);

        $Details = $XPath->query(".//ul[contains(@class, 'jqm-listview')]//li", $Details);

        if(empty($Details->length))
            return false;

        $Details = $Details->item(0);

        $IssueDate = $XPath->query(".//strong[contains(text(), 'Emissão:')]/following-sibling::text()[1]", $Details);

        if(empty($IssueDate->length))
            return false;

        $IssueDate = $IssueDate->item(0)->nodeValue;
        $IssueDate = trim($IssueDate);

        $Tokens = explode('-', $IssueDate);

        if(count($Tokens) == 1)
            return false;

        $this->IssueDate = trim($Tokens[0]);

        $IimeZone = new DateTimeZone('America/Sao_Paulo');
        $Data = DateTime::createFromFormat('d/m/Y H:i:s', $this->IssueDate, $IimeZone);
        $this->IssueDateTimestamp = $Data->getTimestamp();

        return true;

    }

    public function getName() : string {

        return $this->Name;

    }

    public function getCNPJ() : string {

        return $this->CNPJ;

    }

    public function getAddress() : string {

        return $this->Address;

    }

    public function getTaxes() : float {

        return $this->Taxes;

    }

    public function getItems() : array {

        return $this->Items;

    }

    public function getIssueDate() : string {

        return $this->IssueDate;

    }

    public function getIssueDateTimestamp() : int {

        return $this->IssueDateTimestamp;

    }

}