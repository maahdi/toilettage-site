<?php
namespace Toilettage\structureBundle\Entity;

use Toilettage\structureBundle\XML\MyXml;

class KeywordsRepo extends MyXml
{

    public function getGeneralKeywords()
    {
        $xml = new \SimpleXmlElement(file_get_contents($this->getXmlFilePath('keyword')));
        foreach ($xml as $general)
        {
            $keywords = (string) $general->keywords;
        }
        return $keywords;
    }

    public function save($keywords)
    {
        $xml = new \simpleXmlElement(file_get_contents($this->getXmlFilePath('keyword')));
        foreach($xml as $general)
        {
            $general->keywords = $keywords;
        }
        $xml->asXml($this->getXmlFilePath('keyword'));
    }
    public function getHtml()
    {
        return '<div class="Gkeywords admin-c border">
                    <section class="adminGkeywords">
                        <label>Mots-clés généraux</label><textarea class="textareaGkeywords" name="Gkeywords">'.$this->getGeneralKeywords().'</textarea>
                    </section>
                    <section class="btn-adminPanel">
                        <article class="btn-admin maj-Gkeywords">
                            <li>Mettre à jour</li>
                        </article>
                    </section>
                </div>';
    }
    
}
