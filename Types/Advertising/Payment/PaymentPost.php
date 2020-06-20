<?php
namespace fwc\Thing;

/**
 * PaymentPost
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */
class PaymentPost extends ModelPost {  
    public $table = "formpg";
    
    public function add()
    {
        // resume
        $resume = self::resume($this->POST);       
        $this->POST['idcontratos'] = $this->idOwner;
        // add payment
        $this->POST['quitado'] = $this->POST['quitado'] == '' ? null : $this->POST['quitado'];
        parent::created($this->POST);
        // add history
        (new HistoryModel())->setHistory("INSERT", "Payment added: $resume", $this->idOwner, $this->tableOwner);
        return "/admin/advertising/edit/".$this->idOwner;
    }
    
    public function edit(): bool 
    {
        $this->POST['quitado'] = $this->POST['quitado'] == '' ? null : $this->POST['quitado'];
        $idpayment = $this->POST['idformpg'];
        $dataPayment = (new PaymentModel())->getPaymentByIdpayment($idpayment);
        // update payment
        parent::updateById();
        // add history
        $resume = null;
        foreach ($dataPayment[0] as $key => $value) {
            if (array_key_exists($key,$this->POST) && $this->POST[$key] !== $value) {
                $resume .= "'$key' from '" . ($value ?? "null") . "' to '" . ($this->POST[$key] ?? "null")."'; ";
            }
        }
        $summary = "Payment alter: ". addslashes($resume);
        if ($resume) {
            (new HistoryModel())->setHistory("UPDATE", $summary, $this->idOwner, $this->tableOwner);
        }
        return true;        
    }
    
    public function erase() 
    {
        $idpayment = $this->POST['idformpg'];
        parent::delete([ "idformpg" => $idpayment ]);
        (new HistoryModel())->setHistory("DELETE", "Payment deleted: ".self::resume($this->POST), $this->idOwner, $this->tableOwner);
        return true;
    }
    
    static private function resume($POST) 
    {
        $resume = null;
        foreach ($POST as $key => $value) {
            $resume .= " $key = ".($value == '' ? "null" : $value).";";
        } 
        return $resume;
    }
}
