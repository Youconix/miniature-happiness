<?php 
namespace core\models\data;

class HitCollection implements \Iterator {
    private $a_items = array();
    private $a_keys = array();
    private $i_pos = 0;
    private $i_length = 0;

    public function __construct($i_startDate,$i_endDate){
        while($i_startDate < $i_endDate){
            $item = new HitItem(0, $i_startDate);
            $s_key = $item->getKey();

            $this->a_items[$s_key] = $item;
            $this->a_keys[] = $s_key;
            $this->i_length++;

            $i_startDate = mktime(0, 0, 0,date("n",$i_startDate)+1, 1,date("Y",$i_startDate));
        }
    }

    public function add(HitItem $item){
        $s_key = $item->getKey();

        $this->a_items[$s_key]->increaseAmount($item->getAmount());
    }

    /**
     * Returns the current item
     *
     * @return HitItem  The item
     */
    public function current(){
        $s_key = $this->key();
        return $this->a_items[$s_key];
    }

    /**
     * Returns the key of the current item
     *
     * @return string   The key
     */
    public function key(){
        return $this->a_keys[$this->i_pos];
    }

    public function next(){
        $this->i_pos++;
    }
    public function rewind ( ){
        $this->i_pos = 0;
    }

    public function valid (){
        return ($this->i_pos < $this->i_length);
    }
}

class HitItem{
    private $i_amount = 0;
    private $s_key = '';
    private $i_month;
    private $i_year;

    public function __construct($i_amount,$i_datetime){
        $this->i_amount = $i_amount;
        $this->i_month = date('n',$i_datetime);
        $this->i_year = date('Y',$i_datetime);
        $this->s_key = $this->i_month.'-'.$this->i_year;
    }

    public function getKey(){
        return $this->s_key;
    }

    public function increaseAmount($i_amount){
        $this->i_amount += $i_amount;
    }

    public function getAmount(){
        return $this->i_amount;
    }
}
?>