<?php
namespace fwc\Thing;
/**
 * UserPost
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */
class UserPost extends ModelPost {
    protected $table = "user";
    
    public function add(): string {
        $iduser = parent::createNewAndReturnLastId($this->POST);
        return "/admin/user/edit/$iduser";
    }
    
    public function edit(): bool {
        
    }
    
    public function erase() {
        
    }
    
    public function createSqlTable($type = null) {
        $post = array_filter($this->POST);
        if (empty($post)) {
            return "/admin?warning=empty";
        }elseif(filter_var($post['email'], FILTER_VALIDATE_EMAIL) === false) {
            return "/admin?warning=emailwrong";
        } elseif ($post['password'] !== $post['passwordRepeat']) {
            return "/admin?warning=passdiff";
        } elseif(strlen($post['password']) < 8) {
            return "/admin?warning=passlenght";
        } else {
            // sql create table
            $sqlFile = file_get_contents(__DIR__."/createSqlTable.sql");
            $this->connect->query($sqlFile);
            // insert user
            $this->POST['status'] = 1;
            unset($this->POST['passwordRepeat']);
            $this->POST['password'] = password_hash($this->POST['password'],PASSWORD_DEFAULT);
            $iduser = parent::createNewAndReturnLastId($this->POST);
            // login user
            $this->POST['iduser'] = $iduser;
            \fwc\Cms\Auth\Users::login($this->POST);
            return "/admin/user/edit/$iduser";            
        }
    }
}
