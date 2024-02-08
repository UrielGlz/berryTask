<?php
include_once 'Layout.php';

class Paginacion extends EntityRepository{
  private $rs;
  private $currPag = 1;
  private $pageSize = 20;
  private $start;

  private $layout;

  private $tables;
  private $fields = "*";
  private $where = "1=1";
  private $orderBy = '';
  private $GroupBy = '';
  private $join = null;
  private $queryString = null;
  private $actionController = null;

  public function __construct($pag,$pageSize = 20){
    $this->setCurrPage($pag);
    $this->setPageSize($pageSize);
    $this->setStart(($this->getCurrPage() - 1) * $this->getPageSize());
  }
  
  public function setActionController($actionController){
      $this->actionController = $actionController;
  }
  
  public function getActionController(){
      return $this->actionController;
  }
  public function getResultSet(){
    $totalPages = $this->getTotalPages();
    if ($totalPages < 1) {
      return false;
    }
    if ($this->getCurrPage() > $totalPages) {
      return false;
    }
    $query = "select " . $this->getFields() . " from " . $this->getTables();

    $query .= $this->getJoin();

    $query .= " where " . $this->getWhereFilter();
    
    if ($this->getGroupBy()<>'') {
        $query .= " Group By " . $this->getGroupBy();}
    
    $query .= ($this->getOrderBy() != '') ? " order by " . $this->getOrderBy():"";
    $query .= " limit " . $this->getStart() . ", " . $this->getPageSize();
    //echo $query;exit;
    $rs = $this->query($query);
    return $rs;
  }
  
  public function setQueryString($query){
      $this->queryString = $query;
  }
  
  public function getQueryString(){
      return $this->queryString;
  }
  
  public function getResultSetQueryString(){
    $totalPages = $this->getTotalPagesQueryString();
    if ($totalPages < 1) {
      return false;
    }
    if ($this->getCurrPage() > $totalPages) {
      return false;
    }
    $query = $this->getQueryString();
    $query .= " limit " . $this->getStart() . ", " . $this->getPageSize();
    //echo $query;exit;
    $rs = $this->query($query);
    return $rs;
  }


  public function setJoin($join = null) {
      if ($join) {
          $this->join = $join;
      }
  }

  public function getJoin() {
      return " " . $this->join;
  }

  public function setTables($tables){
    $this->tables = $tables;
  }
  private function getTables(){
    return $this->tables;
  }
  public function setWhereFilter($where = '1=1'){
    $this->where = $where;
  }
  private function getWhereFilter(){
    return $this->where;
  }
  public function setFields($fields = "*"){
    $this->fields = $fields;
  }
  private function getFields(){
    return $this->fields;
  }
  public function setOrderBy($orderBy){
    $this->orderBy = $orderBy;
  }
  private function getOrderBy(){
    return $this->orderBy;
  }
  public function setGroupBy($GroupBy = ''){
    $this->GroupBy = $GroupBy;
  }
  private function getGroupBy(){
    return $this->GroupBy;
  }
  public function getTotalPages(){
      if($this->queryString){return $this->getTotalPagesQueryString();}
      
      if ($this->GroupBy<>'')
            $numRows = $this->numRows($this->getTables(),$this->getWhereFilter(),$this->GroupBy,$this->GroupBy);
      else
            $numRows = $this->numRows($this->getTables(),$this->getWhereFilter());
    return ceil($numRows/$this->getPageSize());
  }
  
  public function getTotalPagesQueryString(){
      $result = $this->query($this->getQueryString());      
      return ceil($result->num_rows/$this->getPageSize());
  }
  
  public function numRows($table, $where='1=1', $GroupBy='',$DistinctField='') {
    if ($GroupBy<>'') {
        $query = "select Count(Distinct $DistinctField ) as numRows from $table where $where ";}
 else {
    $query = "select count(*) as numRows from $table where $where";}
    $res = $this->query($query);

    $nr = $res->fetch_assoc();
    return $nr['numRows'];
  }
  private function setCurrPage($pag){
    if($pag <= 1){
      $this->currPag = 1;
      return;
    }
    $this->currPag = $pag;
  }
  public function getCurrPage(){
    return $this->currPag;
  }
  private function setPageSize($pageSize){
    $this->pageSize = $pageSize;
  }
  private function getPageSize(){
    return $this->pageSize;
  }
  private function setStart($start){
    $this->start = $start;
  }
  public function getStart(){
    return $this->start;
  }
  public function setLayout(Layout $layout) {
    $this->layout = $layout;
  }
  private function getLayout(){
    return $this->layout;
  }
  public function showLinks() {
    return $this->getLayout()->mostrar($this, $this->getQueryVars());
  }
  public function getQueryVars($noReturn = 'pag'){
    $queryVars='';
    foreach ($_GET as $clave => $valor){
      if(!strstr($noReturn,$clave)){
      $queryVars .= $clave . "=" . $valor . "&amp;";
    }
  }
    return $queryVars;
  }
  public function getOrderQuery($orderBy = 'id'){
    return $this->getQueryVars($orderBy);
  }
}