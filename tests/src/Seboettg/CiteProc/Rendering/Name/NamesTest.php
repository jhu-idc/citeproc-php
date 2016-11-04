<?php
/**
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Rendering\Names;

use Seboettg\CiteProc\CiteProc;
use Seboettg\CiteProc\Context;
use Seboettg\CiteProc\Locale\Locale;
use Seboettg\CiteProc\Rendering\Group;
use Seboettg\CiteProc\Rendering\Name\Names;
use Seboettg\CiteProc\TestSuiteTestCaseTrait;
use Seboettg\CiteProc\TestSuiteTests;

class NamesTest extends \PHPUnit_Framework_TestCase implements TestSuiteTests
{

    use TestSuiteTestCaseTrait;

    public function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $context = new Context();
        $context->setLocale(new Locale("de-DE"));
        CiteProc::setContext($context);
    }

    public function testRenderSingleEditor()
    {
        $data = "{\"author\": [{\"dropping-particle\": \"\", \"family\": \"Einstein\", \"given\": \"Albert\", \"non-dropping-particle\": \"\", \"static-ordering\": false}],\"editor\": [{\"dropping-particle\": \"de\", \"family\": \"Doe\", \"given\": \"John\", \"non-dropping-particle\": \"la\", \"static-ordering\": false}], \"id\": \"ITEM-1\", \"title\": \"His Anonymous Life\", \"type\": \"book\"}";

        $xml = "<names variable=\"editor\" delimiter=\", \"><name and=\"symbol\" initialize-with=\". \" delimiter=\", \"/><label form=\"short\" prefix=\", \" text-case=\"title\"/></names>";

        $names = new Names(new \SimpleXMLElement($xml));
        $ret = $names->render(json_decode($data));
        $this->assertEquals("J. de la Doe, Hrsg.", $ret);

        $xml = "<names variable=\"editor\" delimiter=\", \"><name and=\"symbol\" delimiter=\", \"/><label form=\"short\" prefix=\", \" text-case=\"title\"/></names>";
        $names = new Names(new \SimpleXMLElement($xml));
        $ret = $names->render(json_decode($data));
        $this->assertEquals("John de la Doe, Hrsg.", $ret);
    }

    public function testRenderAuthorAndEditor()
    {
        $data = "{\"author\": [{\"dropping-particle\": \"\", \"family\": \"Einstein\", \"given\": \"Albert\", \"non-dropping-particle\": \"\"}],\"editor\": [{\"dropping-particle\": \"de\", \"family\": \"Doe\", \"given\": \"John\", \"non-dropping-particle\": \"la\", \"static-ordering\": false}], \"id\": \"ITEM-1\", \"title\": \"His Anonymous Life\", \"type\": \"book\"}";

        $xml = "<group>
                    <names variable=\"author\" delimiter=\"; \" suffix=\" in: \">
                        <name form=\"long\" name-as-sort-order=\"all\" sort-separator=\", \" delimiter=\"; \"/>
                    </names>
                    <text variable=\"title\" text-case=\"title\" suffix=\", \"/>
                    <names variable=\"editor\" delimiter=\", \" prefix=\"(\" suffix=\")\">
                        <name and=\"symbol\" name-as-sort-order=\"all\" initialize-with=\". \" delimiter=\", \"/>
                        <label form=\"short\" prefix=\", \" text-case=\"title\"/>
                    </names>
                </group>";

        $group = new Group(new \SimpleXMLElement($xml));
        $ret = $group->render(json_decode($data));
        $this->assertEquals("Einstein, Albert in: His Anonymous Life, (la Doe, J. de, Hrsg.)", $ret);

        //two authors
        $data2 = "{\"author\": [{\"dropping-particle\": \"\", \"family\": \"Einstein\", \"given\": \"Albert\", \"non-dropping-particle\": \"\"}, {\"dropping-particle\": \"\", \"family\": \"Skłodowska Curie\", \"given\": \"Marie\", \"non-dropping-particle\": \"\"}],\"editor\": [{\"dropping-particle\": \"de\", \"family\": \"Doe\", \"given\": \"John\", \"non-dropping-particle\": \"la\", \"static-ordering\": false}], \"id\": \"ITEM-1\", \"title\": \"Das Leben des Brian\", \"type\": \"book\"}";
        $ret2 = $group->render(json_decode($data2));
        $this->assertEquals("Einstein, Albert; Skłodowska Curie, Marie in: Das Leben des Brian, (la Doe, J. de, Hrsg.)", $ret2);

    }

    public function testRenderMultipleAuthors()
    {
        $xml = "<names variable=\"author\" name-as-sort-order=\"all\" delimiter=\", \" prefix=\"(\" suffix=\")\"><name form=\"short\" and=\"symbol\" delimiter=\", \"/><label form=\"short\" prefix=\", \" text-case=\"title\"/></names>";
        $names = new Names(new \SimpleXMLElement($xml));

        // two names
        $data = "{\"author\": [{\"dropping-particle\": \"de\", \"family\": \"Doe\", \"given\": \"John\", \"non-dropping-particle\": \"la\", \"static-ordering\": false}, {\"dropping-particle\": \"\", \"family\": \"Curie\", \"given\": \"Marie\", \"non-dropping-particle\": \"\", \"static-ordering\": false}], \"id\": \"ITEM-1\", \"title\": \"Her Anonymous Life\", \"type\": \"book\"}";
        $this->assertEquals("(la Doe &#38; Curie)", $names->render(json_decode($data)));

        // three names
        $data = "{\"author\": [{\"dropping-particle\": \"de\", \"family\": \"Doe\", \"given\": \"John\", \"non-dropping-particle\": \"la\", \"static-ordering\": false}, {\"dropping-particle\": \"\", \"family\": \"Curie\", \"given\": \"Marie\", \"non-dropping-particle\": \"\", \"static-ordering\": false}, {\"dropping-particle\": \"\", \"family\": \"Einstein\", \"given\": \"Albert\", \"non-dropping-particle\": \"\", \"static-ordering\": false}], \"id\": \"ITEM-1\", \"title\": \"Her Anonymous Life\", \"type\": \"book\"}";
        $this->assertEquals("(la Doe, Curie, &#38; Einstein)", $names->render(json_decode($data)));

        // four names
        $data = "{\"author\": [{\"dropping-particle\": \"de\", \"family\": \"Doe\", \"given\": \"John\", \"non-dropping-particle\": \"la\", \"static-ordering\": false}, {\"dropping-particle\": \"\", \"family\": \"Curie\", \"given\": \"Marie\", \"non-dropping-particle\": \"\", \"static-ordering\": false}, {\"dropping-particle\": \"\", \"family\": \"Einstein\", \"given\": \"Albert\", \"non-dropping-particle\": \"\", \"static-ordering\": false}, {\"dropping-particle\": \"\", \"family\": \"Röntgen\", \"given\": \"Wilhelm Conrad\", \"non-dropping-particle\": \"\", \"static-ordering\": false}], \"id\": \"ITEM-1\", \"title\": \"Her Anonymous Life\", \"type\": \"book\"}";
        $this->assertEquals("(la Doe, Curie, Einstein, &#38; Röntgen)", $names->render(json_decode($data)));
    }

    public function testRenderMultipleAuthorsEtAl()
    {
        $xml = "<names variable=\"author\" delimiter=\"; \" prefix=\"(\" suffix=\")\"><name delimiter=\"; \" form=\"short\" name-as-sort-order=\"all\" sort-separator=\", \" and=\"symbol\" et-al-min=\"4\" et-al-use-first=\"2\"/></names>";
        $names = new Names(new \SimpleXMLElement($xml));

        // four names
        $data = "{\"author\": [{\"dropping-particle\": \"de\", \"family\": \"Doe\", \"given\": \"John\", \"non-dropping-particle\": \"la\", \"static-ordering\": false}, {\"dropping-particle\": \"\", \"family\": \"Curie\", \"given\": \"Marie\", \"non-dropping-particle\": \"\", \"static-ordering\": false}, {\"dropping-particle\": \"\", \"family\": \"Einstein\", \"given\": \"Albert\", \"non-dropping-particle\": \"\", \"static-ordering\": false}, {\"dropping-particle\": \"\", \"family\": \"Röntgen\", \"given\": \"Wilhelm Conrad\", \"non-dropping-particle\": \"\", \"static-ordering\": false}], \"id\": \"ITEM-1\", \"title\": \"Her Anonymous Life\", \"type\": \"book\"}";
        $this->assertEquals("(la Doe; Curie; u. a.)", $names->render(json_decode($data)));
    }

    public function testRenderMultipleAuthorEtAlElement()
    {

        $xml = "<names variable=\"author\" delimiter=\"; \">
                    <name form=\"long\" name-as-sort-order=\"all\" and=\"symbol\" delimiter=\"; \" et-al-min=\"4\" et-al-use-first=\"2\"/>
                    <et-al term=\"et-al\" font-style=\"italic\"/>
                </names>";
        $names = new Names(new \SimpleXMLElement($xml));

        // four names
        $data = "{\"author\": [{\"dropping-particle\": \"de\", \"family\": \"Doe\", \"given\": \"John\", \"non-dropping-particle\": \"la\", \"static-ordering\": false}, {\"dropping-particle\": \"\", \"family\": \"Curie\", \"given\": \"Marie\", \"non-dropping-particle\": \"\", \"static-ordering\": false}, {\"dropping-particle\": \"\", \"family\": \"Einstein\", \"given\": \"Albert\", \"non-dropping-particle\": \"\", \"static-ordering\": false}, {\"dropping-particle\": \"\", \"family\": \"Röntgen\", \"given\": \"Wilhelm Conrad\", \"non-dropping-particle\": \"\", \"static-ordering\": false}], \"id\": \"ITEM-1\", \"title\": \"Her Anonymous Life\", \"type\": \"book\"}";
        $this->assertEquals("la Doe, John de; Curie, Marie; <i>u. a.</i>", $names->render(json_decode($data)));
    }

    public function testRenderTestSuite()
    {
        $this->_testRenderTestSuite("name_");
    }
}
