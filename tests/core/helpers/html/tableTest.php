<?php
if (! defined('NIV')) {
    define('NIV', dirname(__FILE__) . '/../../../../');
}

class testTable extends \tests\GeneralTest
{

    private $s_content = 'test table cell';

    private $tableFactory;

    public function __construct()
    {
        parent::__construct();
        
        require_once (NIV . 'core/helpers/HTML.inc.php');
        require_once (NIV . 'core/helpers/html/Div.php');
        
        $helper = new core\helpers\html\HTML();
        $this->tableFactory = $helper->tableFactory();
    }

    /**
     * Tests of a normal table cell
     *
     * @test
     */
    public function normalCell()
    {
        $object = $this->tableFactory->cell($this->s_content);
        $s_expected = '<td >' . $this->s_content . '</td>';
        $this->assertEquals($s_expected, $object->generateItem());
    }

    /**
     * Tests of a table cell with spans
     *
     * @test
     */
    public function spanCell()
    {
        $object = $this->tableFactory->cell($this->s_content);
        $object->setRowspan(3);
        $s_expected = '<td rowspan="3" >' . $this->s_content . '</td>';
        $this->assertEquals($s_expected, $object->generateItem());
        
        $object = $this->tableFactory->cell($this->s_content);
        $object->setColspan(3);
        $s_expected = '<td colspan="3" >' . $this->s_content . '</td>';
        $this->assertEquals($s_expected, $object->generateItem());
    }

    /**
     * Test of creating a empty row
     *
     * @test
     */
    public function tableRow()
    {
        $object = $this->tableFactory->row();
        $s_expected = "<tr >\n</tr>\n";
        $this->assertEquals($s_expected, $object->generateItem());
    }

    /**
     * Test of adding a wrong cell
     *
     * @test
     * @expectedException Exception
     */
    public function tableRowInvalidCell()
    {
        $this->tableFactory->row()->addCell('test');
    }

    /**
     * Test of adding a cell
     *
     * @test
     */
    public function tableRowAddCell()
    {
        $cell = $this->tableFactory->cell('');
        
        $object = $this->tableFactory->row();
        $object->addCell($cell);
        $s_expected = "<tr >\n<td ></td>\n</tr>\n";
        $this->assertEquals($s_expected, $object->generateItem());
    }

    /**
     * Test of creating a cell
     *
     * @test
     */
    public function tableRowCreateCell()
    {
        $object = $this->tableFactory->row();
        $object->createCell('');
        $s_expected = "<tr >\n<td ></td>\n</tr>\n";
        $this->assertEquals($s_expected, $object->generateItem());
    }

    /**
     * Test of creating an empty table
     *
     * @test
     */
    public function table()
    {
        $s_expected = "<table >\n<tbody>\n</tbody>\n</table>\n";
        $this->assertEquals($s_expected, $this->tableFactory->table()
            ->generateItem());
    }

    /**
     * Test of adding an invalid header
     *
     * @test
     * @expectedException Exception
     */
    public function tableInvalidHeader()
    {
        $this->tableFactory->table()->addHeader('');
    }

    /**
     * Test of adding a header
     *
     * @test
     */
    public function tableAddHeader()
    {
        $header = $this->tableFactory->row();
        
        $table = $this->tableFactory->table();
        $table->addHeader($header);
        $s_expected = "<table >\n<thead>\n<tr >\n</tr>\n</thead>\n<tbody>\n</tbody>\n</table>\n";
        $this->assertEquals($s_expected, $table->generateItem());
    }

    /**
     * Test of adding a row
     * Same functionality as addRow
     *
     * @test
     */
    public function tableSetValue()
    {
        $row = $this->tableFactory->row();
        
        $table = $this->tableFactory->table();
        $table->setValue($row);
        $s_expected = "<table >\n<tbody>\n<tr >\n</tr>\n</tbody>\n</table>\n";
        $this->assertEquals($s_expected, $table->generateItem());
    }

    /**
     * Test of adding an invalid row
     *
     * @test
     * @expectedException Exception
     */
    public function tableAddInvalidRow()
    {
        $this->tableFactory->table()->addRow('');
    }

    /**
     * Test of adding a row
     *
     * @test
     */
    public function tableAddRow()
    {
        $row = $this->tableFactory->row();
        
        $table = $this->tableFactory->table();
        $table->addRow($row);
        $s_expected = "<table >\n<tbody>\n<tr >\n</tr>\n</tbody>\n</table>\n";
        $this->assertEquals($s_expected, $table->generateItem());
    }

    /**
     * Test of adding an invalid footer
     *
     * @test
     * @expectedException Exception
     */
    public function tableInvalidFooter()
    {
        $this->tableFactory->table()->addFooter('');
    }

    /**
     * Test of adding a footer
     *
     * @test
     */
    public function tableAddFooter()
    {
        $footer = $this->tableFactory->row();
        
        $table = $this->tableFactory->table();
        $table->addFooter($footer);
        $s_expected = "<table >\n<tbody>\n</tbody>\n<tfoot>\n<tr >\n</tr>\n</tfoot>\n</table>\n";
        $this->assertEquals($s_expected, $table->generateItem());
    }
}