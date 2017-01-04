<?php


namespace iRestMyCase\Core\Interfaces;

interface DaoInterface{


     public function create($model);

     public function read($model);

     public function update($model);

     public function delete($model);

     public function getModelNames();


}
