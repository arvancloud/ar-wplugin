<?php

if(!class_exists('Link_List_Table'))
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

new ArvanCloudDomains();

class ArvanCloudDomains extends WP_List_Table {

    public function __construct() {
        parent::__construct();
        ?> 
        <div class="wrap">
                <div id="icon-users" class="icon32"></div>
                <h1 class="wp-heading-inline">کدهای متصل شده</h1>
                <form method="get">
                            <input type="hidden" name="page" value="<?php echo $_REQUEST['page']; ?>" />
                            <?php
                            $this->prepare_items();
                            $this->search_box('جستجو', 'search');
                         ?>
                </form>
                <?php $this->display(); ?>
            </div>
        <?php
    }

    public function prepare_items() {
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();
        
        $data = $this->table_data();
        
        $perPage = 20;
        $currentPage = $this->get_pagenum();
        $totalItems = count($data);

        $this->set_pagination_args( array(
            'total_items' => $totalItems,
            'per_page'    => $perPage
        ) );

        $data = array_slice($data,(($currentPage-1)*$perPage),$perPage);

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $data;
    }

    public function get_columns() {
        return array(
            'id'        => 'شناسه',
            'domain'    => 'دامنه',
            'status'    => 'وضعیت DNS',
            'cloud_security'    => 'امنیت ابری',
            'remove'  => 'حذف'
        );
    }

    public function get_hidden_columns()
    {
        return array();
    }

    public function get_sortable_columns()
    {
        return array(
            'id' => array('ID', true)
        );
    }

    private function table_data() {
        require_once('requests.php');
        $domains = json_decode(ArRequests::getDomains());

        if (!isset($domains->data))
            return array();

        $table_data = array();
        $i = 1;

        if (isset($_GET['order'])) {
            if ($_GET['order'] == 'asc') {
                sort($domains->data);
            }else {
                rsort($domains->data);
            }
        }
        
        foreach ($domains->data as $domain) {
            array_push($table_data, [
                'id'                =>  '<a href="#"> '. $i .' </a>',
                'domain'            =>  '<a href="#"> '. $domain->domain .' </a>',
                'status'            =>  $this->statusToHumanReadable($domain->status),
                'cloud_security'    =>  ($domain->services->cloud_security) ? '<code style="color: green;"> فعال </code>' : '<code style="color: red;"> غیرفعال </code>',
                'remove'            =>  '<a href="?action=remove&uuid='. $domain->id .'"> <span style="color:red;"> حذف </span> </a>'
            ]);
            $i++;
        }
        
        unset($i);
        return $table_data;
    }

    public function column_default( $item, $column_name )
    {
        switch ($column_name) {
            case 'id':
            case 'domain':
            case 'status':
            case 'cloud_security':
            case 'remove':
                return $item[$column_name];
            default:
                return $item[$column_name];
                break;
        }
    }

    private function sort_data( $a, $b )
    {
        $result = 0;

        if (!empty($_GET['orderby'])) {
            if ($a[$_GET['orderby']] > $b[$_GET['orderby']]) {
                $result = 1;
            }else if($a[$_GET['orderby']] < $b[$_GET['orderby']]) {
                $result = -1;
            }
        }else {
            $result = -1;
        }

        return $result; 
    }

    private function statusToHumanReadable($status) {
        switch ($status) {
            case 'pending':
                return 'در انتظار';
            default:
                return $status;
        }
    }
}