<?php
/**
 * Created by PhpStorm.
 * User: yasiel
 * Date: 15/03/20
 * Time: 12:47
 */


if (!defined('_PS_VERSION_')) {
    exit;
}

 //http://www.mazmorragames.com.pe/index.php?controller=items&fc=module&module=ps_download_items

class Ps_Article extends Module
{
    public function __construct()
    {
        $this->name = 'ps_article';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Sacavix Team';
        $this->controllers = array('articles','showArticles',);

        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('Articulos MazmorraGames');
        $this->description = $this->l('Este modulo es para publicar articulos para tiendas B2B ');
        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];


    }

    public function getTabs()
    {

        return [
            [   "name" => 'Articulos',
                "parent_class_name" => 'AdminDashboard',
                "class_name" => 'AdminArticle',
                "visible" => true]
        ];
    }

    public function getContent()
    {
        $redirectLink = $this->context->link->getAdminLink("AdminArticle");
        Tools::redirectAdmin($redirectLink);
    }


    public function install()
    {
        $hook = $this->registerHook('displayHome');


        return   $this->installDB() && parent::install();
    }

    public function hookDisplayHome($params)
    {
        $recentSQL = "SELECT * FROM " . _DB_PREFIX_ . "articles  WHERE active='1' ORDER BY pub_date DESC";
        $recent = Db::getInstance()->ExecuteS($recentSQL);

        $this->context->smarty->assign([
            'recent' => $recent,
            'link' => _PS_BASE_URL_ . "/"
        ]);
        return $this->display(__FILE__, 'articles.tpl');
    }

    public function unregisterHooks() {
        return $this->setupHooks([]);
    }

    public function uninstall()
    {
        return $this->unregisterHooks() && $this->uninstallTab() && $this->uninstallDB() &&parent::uninstall();
    }


    public function uninstallTab()
    {
        $id_tab = (int) Tab::getClassNameById('AdminArticle');
        $tab = new Tab($id_tab);
        return $tab->delete();
    }

    private function uninstallDB()
    {
        $sqlStatements = "DROP TABLE IF NOT EXISTS ps_download_items";

        $database = \PrestaShop\PrestaShop\Adapter\Entity\Db::getInstance();
        try {
            $database->execute($sqlStatements);
            return true;
        } catch (Exception $exception) {
            return false;
        }
    }

    private function installDB()
    {
        $sqlStatements = "CREATE TABLE IF NOT EXISTS ps_articles (
                         `id` INT NOT NULL AUTO_INCREMENT ,
                         `title` VARCHAR(255) NOT NULL ,
                         `short_description` VARCHAR(255) NOT NULL ,
                          `article` VARCHAR(5000) NOT NULL ,
                         `active` BOOLEAN ,                         
                         `image` VARCHAR(255) NOT NULL ,
                         `user` VARCHAR(255) NOT NULL ,
                         `userID` VARCHAR(255) NOT NULL ,
                         `review` INT(11) NOT NULL ,
                         `pub_date` VARCHAR(255) NOT NULL ,
                          PRIMARY KEY (`id`)) ENGINE = InnoDB;";
        
        $database = \PrestaShop\PrestaShop\Adapter\Entity\Db::getInstance();
        try {
            $database->execute($sqlStatements);
            return true;
        } catch (Exception $exception) {
            return false;
        }
    }
}
