<?php

/**
 * 项目间通信用
 */
class AppAccessAction extends AppAccess {

    public function categoryList() {
        die(json_encode(D('Category')->categoryList()));
    }   

}
