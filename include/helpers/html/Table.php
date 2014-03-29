<?php
namespace core\helpers\html;

class TableFactory {
	private static $_instance;
	
	public static function getInstance(){
		if( is_null(self::$_instance) ){
			self::$_instance = new TableFactory();
		}
		
		return self::$_instance;
	}
	
	public function table(){
		return new Table();
	}
	
	public function row(){
		return new TableRow();
	}
	
	public function cell($s_content){
		return new TableCell($s_content);
	}
}
	
class Table extends HtmlItem {
	private $obj_header = null;
	private $a_rows;
	private $obj_footer = null;
	private $i_cellspacing = -1;
	private $i_cellpadding = -1;

	/**
	 * Generates a new table element
	 */
	public function __construct(){
		$this->s_tag = "<table{cellspacing}{cellpadding} {between}>\n{value}</table>\n";

		$this->a_rows = array();
	}
	
	/**
	 * Adds the table header
	 * 
	 * @param TableRow $obj_row		The row
	 */
	public function addHeader($obj_row){
		if( !($obj_row instanceof core\helpers\html\TableRow) ){
			throw new \Exception('Invalid input in Table:addHeader. Only a TableRow is allowed. Found '.get_class($obj_row).'.');
		}
		
		$this->obj_header = $obj_row;
		return $this;
	}
	
	/**
	 * Sets the content of the table.
	 * Overwrites any added content
	 *
	 * @param   TableRow $obj_row  The row to add
	 * @throws Exception if $obj_row is the wrong type
	 */
	public function setValue($obj_row){
		$this->a_rows = array();
	
		return $this->addRow($obj_row);
	}
	
	/**
	 * Adds a row
	 *
	 * @param  TableRow $obj_row  The row to add
	 * @throws Exception if $obj_row is the wrong type
	 */
	public function addRow($obj_row){
		if( !($obj_row instanceof TableRow) ){
			throw new \Exception('Invalid input in Table:addRow. Only a TableRow is allowed. Found '.get_class($obj_row).'.');
		}
	
		$this->a_rows[] = $obj_row;
	
		return $this;
	}

	/**
	 * Adds the table footer
	 *
	 * @param TableRow $obj_row		The row
	 */
	public function addFooter($obj_row){
		if( !($obj_row instanceof TableRow) ){
			throw new \Exception('Invalid input in Table:addFooter. Only a TableRow is allowed. Found '.get_class($obj_row).'.');
		}

		if( substr($s_cell, 0,3) != '<td' )		$s_cell = '<td>'.$s_cell.'</td>';
		$this->a_footer[] = $s_cell;

		return $this;
	}

	/**
	 * Sets the cell spacing
	 *
	 * @param int $i_spacing    The cell spacing
	 */
	public function setSpacing($i_spacing){
		if ($i_spacing >= 0){
			$this->i_cellspacing = $i_spacing;
		}

		return $this;
	}

	/**
	 * Sets the cell padding
	 *
	 * @param int $i_padding    The cell padding
	 */
	public function setPadding($i_padding){
		if ($i_padding >= 0){
			$this->i_cellpadding = $i_padding;
		}

		return $this;
	}

	/**
	 * Generates the (X)HTML-code
	 *
	 * @see HtmlItem::generateItem()
	 * @return  String  The (X)HTML code
	 */
	public function generateItem(){
		/* Generate header */
		if( !is_null($this->obj_header) ){
			$this->s_value = "<thead>\n".$this->obj_header->generateItem()."</thead>\n";
		}
		
		/* Generate rows */
		$this->s_value .= "<tbody>\n";
		foreach($this->a_rows AS $obj_row){
			$this->s_value .= $obj_row->generateItem();
		}
		$this->s_value .= "</tbody>\n";

		/* Generate footer */
		if( !is_null($this->obj_footer) ){
			$this->s_value .= "<tfoot>\n".$this->obj_footer->generateItem()."</tfoot>\n";
		}

		$this->i_cellspacing != -1 ? $s_spacing = ' cellspacing="' . $this->i_cellspacing . '"' : $s_spacing = '';
		$this->i_cellpadding != -1 ? $s_padding = ' cellpadding="' . $this->i_cellpadding . '"' : $s_padding = '';

		$this->s_tag = str_replace(array('{cellspacing}', '{cellpadding}'), array($s_spacing, $s_padding), $this->s_tag);

		return parent::generateItem();
	}
}

class TableRow extends HtmlItem {
	private $a_cells;

	/**
	 * Generates a new table row element
	 */
	public function __construct(){
		$this->s_tag = "<tr {between}>\n{value}</tr>\n";

		$this->a_cells = array();
	}

	/**
	 * Adds a table cell
	 *
	 * @param TableCell $obj_cell The table cell
	 * @throws Exception    If the content is from the wrong type
	 */
	public function addCell($obj_cell){
		if( !($obj_row instanceof core\helpers\html\TableCell) ){
			throw new \Exception('Invalid input in TableRow:addCell. Only a TableCell is allowed. Found '.get_class($obj_cell).'.');
		}

		$this->a_cells[] = $obj_cell;

		return $this;
	}

	/**
	 * Creates a table cell and adds it
	 *
	 * @param String $s_content The content of the cell. Also accepts a subtype of CoreHtmlItem
	 */
	public function createCell($s_content){
		$obj_cell = new TableCell($s_content);
		$this->a_cells[] = $obj_cell;

		return $this;
	}

	/**
	 * Sets the value(s) of the table row
	 *
	 * @param String/array $s_value The value(s) to add
	 */
	public function setValue($s_value){
		if (is_array($s_value)){
			foreach ($s_value AS $s_item){
				$this->createCell($s_item);
			}
		} 
		else {
			$this->createCell($s_value);
		}

		return $this;
	}

	/**
	 * Generates the (X)HTML-code
	 *
	 * @see HtmlItem::generateItem()
	 * @return  String  The (X)HTML code
	 */
	public function generateItem(){
		/* Generate row */
		foreach($this->a_cells AS $obj_cell){
			$this->s_value .= $obj_cell->generateItem()."\n";
		}

		return parent::generateItem();
	}
}

class TableCell extends HtmlItem {
	private $i_rowspan = 0;
	private $i_colspan = 0;

	/**
	 * Generates a new table cell element
	 *
	 * @param String $s_value The value of the cell. Also accepts a subtype of CoreHtmlItem
	 */
	public function __construct($s_value){
		$this->s_tag = "<td {between}{span}>{value}</td>";
		$this->setValue($s_value);
	}

	/**
	 * Sets the rowspan of the table cell
	 *
	 * @param int $i_rowspan    The rowspan
	 */
	public function setRowspan($i_rowspan){
		if ($i_rowspan >= 0)
		$this->i_rowspan = $i_rowspan;

		return $this;
	}

	/**
	 * Sets the colspan of the table cell
	 *
	 * @param int $i_colspan    The colspan
	 */
	public function setColspan($i_colspan){
		if ($i_colspan >= 0)
		$this->i_colspan = $i_colspan;

		return $this;
	}

	/**
	 * Generates the (X)HTML-code
	 *
	 * @see HtmlItem::generateItem()
	 * @return  String  The (X)HTML code
	 */
	public function generateItem(){
		$s_span = '';
		if ($this->i_colspan > 0)
		$s_span .= 'colspan="' . $this->i_colspan . '" ';
		if ($this->i_rowspan > 0)
		$s_span .= 'rowspan="' . $this->i_rowspan . '" ';

		$this->s_tag = str_replace('{span}', $s_span, $this->s_tag);

		return parent::generateItem();
	}
}
?>