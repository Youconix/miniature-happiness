<?php
class HTML_Table extends HtmlItem {
	private $a_header;
	private $a_rows;
	private $a_footer;
	private $i_cellspacing = -1;
	private $i_cellpadding = -1;

	/**
	 * Generates a new table element
	 */
	public function __construct() {
		$this->s_tag = "<table{cellspacing}{cellpadding} {between}>\n{value}\n</table>\n";

		$this->a_header = array();
		$this->a_rows = array();
		$this->a_footer = array();
	}

	/**
	 * Adds a header cell
	 *
	 * @param String/HTML_TableCell $s_cell		The table cell(content)
	 */
	public function addHeaderCell($s_cell) {
		if (is_object($s_cell)) {
			if (get_class($s_cell) != 'HTML_TableCell')
			throw new Exception("Unexpected input in Table:addheaderCell. Expect string or TableCell");

			$s_cell = $s_cell->generateItem();
		}

		if( substr($s_cell, 0,3) != '<td' )		$s_cell = '<td>'.$s_cell.'</td>';
		$this->a_header[] = $s_cell;

		return $this;
	}

	/**
	 * Adds a footer cell
	 *
	 * @param String/HTML_TableCell $s_cell		The table cell(content)
	 */
	public function addFooterCell($s_cell) {
		if (is_object($s_cell)) {
			if (get_class($s_cell) != 'HTML_TableCell')
			throw new Exception("Unexpected input in Table:addFooterCell. Expect string or TableCell");

			$s_cell = $s_cell->generateItem();
		}

		if( substr($s_cell, 0,3) != '<td' )		$s_cell = '<td>'.$s_cell.'</td>';
		$this->a_footer[] = $s_cell;

		return $this;
	}

	/**
	 * Sets the content of the table.
	 * Overwrites any added content
	 *
	 * @param   String/HtmlTableRow $s_row  The row to add
	 * @throws Exception if $row is the wrong type
	 */
	public function setValue($s_row) {
		$this->a_rows = array();

		return $this->addRow($s_row);
	}

	/**
	 * Adds a row
	 *
	 * @param  String/HtmlTableRow $s_row  The row to add
	 * @throws Exception if $row is the wrong type
	 */
	public function addRow($s_row) {
		if (is_object($s_row)) {
			if (get_class($s_row) != 'HTML_TableRow')
			throw new Exception("Unexpected input in Table:addRow. Expect string or TableRow");

			$s_row = $s_row->generateItem();
		}

		$this->a_rows[] = $s_row;

		return $this;
	}

	/**
	 * Sets the cell spacing
	 *
	 * @param int $i_spacing    The cell spacing
	 */
	public function setSpacing($i_spacing) {
		if ($i_spacing >= 0) {
			$this->i_cellspacing = $i_spacing;
		}

		return $this;
	}

	/**
	 * Sets the cell padding
	 *
	 * @param int $i_padding    The cell padding
	 */
	public function setPadding($i_padding) {
		if ($i_padding >= 0) {
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
	public function generateItem() {
		/* Generate header */
		if (count($this->a_header) > 0) {
			$this->s_value = "<thead>\n<tr>\n";
			foreach ($this->a_header AS $s_cell) {
				$this->s_value .= $s_cell . "\n";
			}
			$this->s_value .= "</tr>\n</thead>\n";
		}

		/* Generate footer */
		if (count($this->a_footer) > 0) {
			$this->s_value .= "<tfoot>\n<tr>\n";
			foreach ($this->a_footer AS $s_cell) {
				$this->s_value .= $s_cell . "\n";
			}
			$this->s_value .= "</tr>\n</tfoot>\n";
		}

		/* Generate rows */
		$i_rows = count($this->a_rows);
		if( $i_rows > 0 ){
			$this->s_value .= "<tbody>\n";
			 
			for ($i = 0; $i < $i_rows; $i++) {
				$this->s_value .= $this->a_rows[$i];
				$this->s_value .= "\n";
			}
			 
			$this->s_value .= "</tbody>";
		}

		$this->i_cellspacing != -1 ? $s_spacing = ' cellspacing="' . $this->i_cellspacing . '"' : $s_spacing = '';
		$this->i_cellpadding != -1 ? $s_padding = ' cellpadding="' . $this->i_cellpadding . '"' : $s_padding = '';

		$this->s_tag = str_replace(array('{cellspacing}', '{cellpadding}'), array($s_spacing, $s_padding), $this->s_tag);

		return parent::generateItem();
	}
}

class HTML_TableRow extends HtmlItem {
	private $a_cells;

	/**
	 * Generates a new table row element
	 */
	public function __construct() {
		$this->s_tag = "<tr {between}>\n{value}\n</tr>";

		$this->a_cells = array();
	}

	/**
	 * Adds a table cell
	 *
	 * @param String/HTML_TableCell $s_cell The table cell
	 * @throws Exception    If the content is from the wrong type
	 */
	public function addCell($s_cell) {
		if (is_object($s_cell)) {
			if (get_class($s_cell) != 'HTML_TableCell')
			throw new Exception("Unexpected input in TableRow:addcell. Expect string or TableCell");

			$s_cell = $s_cell->generateItem();
		}

		$this->a_cells[] = $s_cell;

		return $this;
	}

	/**
	 * Creates a table cell and adds it
	 *
	 * @param String $s_content The content of the cell. Also accepts a subtype of CoreHtmlItem
	 */
	public function createCell($s_content) {
		$s_content = $this->parseContent($s_content);

		$obj_cell = new HTML_TableCell($s_content);
		$this->a_cells[] = $obj_cell->generateItem();

		return $this;
	}

	/**
	 * Sets the value(s) of the table row
	 *
	 * @param String/array $s_value The value(s) to add
	 */
	public function setValue($s_value) {
		if (is_array($s_value)) {
			foreach ($s_value AS $s_item) {
				$this->createCell($s_item);
			}
		} else {
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
	public function generateItem() {
		/* Generate row */
		$i_cells = count($this->a_cells) - 1;
		for ($i = 0; $i <= $i_cells; $i++) {
			$this->s_value .= $this->a_cells[$i];
			if ($i < $i_cells)
			$this->s_value .= "\n";
		}

		return parent::generateItem();
	}
}

class HTML_TableCell extends HtmlItem {
	private $i_rowspan = 0;
	private $i_colspan = 0;

	/**
	 * Generates a new table cell element
	 *
	 * @param String $s_value The value of the cell. Also accepts a subtype of CoreHtmlItem
	 */
	public function __construct($s_value) {
		$this->s_tag = "<td {between}{span}>{value}</td>";
		$this->setValue($s_value);
	}

	/**
	 * Sets the rowspan of the table cell
	 *
	 * @param int $i_rowspan    The rowspan
	 */
	public function setRowspan($i_rowspan) {
		if ($i_rowspan >= 0)
		$this->i_rowspan = $i_rowspan;

		return $this;
	}

	/**
	 * Sets the colspan of the table cell
	 *
	 * @param int $i_colspan    The colspan
	 */
	public function setColspan($i_colspan) {
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
	public function generateItem() {
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