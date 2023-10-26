<?php 
    $limit = 3;
    $get = isset($_GET)? $_GET : false;
    //Xử lý lọc
    $filters = "";
    if(!empty($get['status'])){
        if($get['status'] == 'active' ||$get['status'] == 'inactive'){
            $status = $get['status'] == 'active' ? 1: 0;
            $filters.='WHERE stastus ='.$status;
            
        }
    }

    if (!empty($get['group_id'])) {
        $groupId = $get['group_id'];
        $filters.=getOperator($filters).' user.group_id='.$groupId;
    }
    
    if (!empty($get['keyword'])) {
        $keyword = $get['keyword'];
        $filters.=getOperator($filters)." (user.name LIKE '%".$keyword."%' OR user.email LIKE '%".$keyword."%')";
    }

    //Xử lý phân trang
    //Tổng số trang: số lượng bản ghi / số lượng bản ghi 1 trang => Làm tròn lên
    $totalRows = getRows("SELECT id FROM user");
    $maxPage = ceil($totalRows / $limit);

    //2. Lấy trang hiện tại
    if (!empty($get['page']) && filter_var($get['page'], FILTER_VALIDATE_INT, [
        'options' => [
        'min_range' => 1,
        'max_range' => $maxPage,
        ]
    ])!== false) {
        $page = $get['page'];
    } else {
        $page = 1;
    }

    // 3.Offset
    $offset = $limit* ($page -1);


    $user = get("SELECT user.*, groups.name AS group_name 
                FROM user 
                INNER JOIN groups ON user.group_id=groups.id $filters
                ORDER BY user.id DESC LIMIT $limit OFFSET $offset");
    
    $groups = get("SELECT * FROM groups ORDER BY name");


?>

<h2>Danh sách người dùng</h2>

<form action="" class="mb-3">
    <div class="row">
        <div class="col-3">
            <select name="status" class="form-select">
                <option value="all">Tất cả trạng thái</option>
                <option value="active" <?php echo !empty($get['status']) && $get['status'] == 'active' ? 'selected' : false; ?> >Kích hoạt</option>
                <option value="inactive" <?php echo !empty($get['status']) && $get['status'] == 'inactive' ? 'selected' : false; ?> >Chưa kích hoạt</option>
            </select>
        </div>
        <div class="col-3">
            <select name="group_id" class="form-select">
                <option value="0">Tất cả các nhóm</option>

                <?php 
                    if (!empty($groups)) {
                        foreach ($groups as $group) {
                            $selected = !empty($get['group_id']) && $get['group_id'] == $group['id'] ? 'selected' : false;
                            echo "<option value='".$group['id']."' $selected>".$group['name']."</option>";
                        }
                    }
                ?>
                
            </select>
        </div>
        <div class="col-4">
            <input type="search" class="form-control" placeholder="Từ khoá" name="keyword" id="" 
            value="<?php echo $get['keyword'] ?? false; ?>">
        </div>
        <div class="col-2 d-grid">
            <button type="submit" class="btn btn-primary">Tìm kiếm</button>
        </div>
    </div>
</form>
<table class="table table-bordered">
    <thead>
        <th>STT</th>
        <th>Tên</th>
        <th>Email</th>
        <th>Nhóm</th>
        <th>Trạng thái</th>
        <th>Thời gian</th>
        <th>Sửa</th>
        <th>Xoá</th>        
    </thead>

    <tbody>
        <?php 
            if(!empty($user)):
                foreach ($user as $index => $user ):
        ?>
        <td><?php echo $index +1 ;?></td>
        <td><?php echo $user['name']; ?></td>
        <td><?php echo $user['email']; ?></td>
        <td>
            <?php echo $user['group_name']; ?>
        </td>
        <td> <?php echo $user['stastus'] == 1 ? 'Kích hoạt' : 'Chưa kích hoạt'; ?> </td>
        <td>
            <?php 
                echo getDateFormat($user['created_at'], 'd/M/y H:i:s') ;
            ?>
        </td>
        <td>
            <a href="#" class="btn btn-warning">Sửa</a>
        </td>
        <td>
            <a href="#" class="btn btn-danger">Xoá</a>
        </td>
    </tbody>
    <?php 
        endforeach;
    endif;
    ?>
</table>
<?php
if ($maxPage > 1):
    ?>
<nav aria-label="Page navigation example" class="d-flex justify-content-end">
    <ul class="pagination">
        <?php
            if ($page > 1) {
                echo '<li class="page-item"><a class="page-link" href="'.getPaginateUrl($page-1).'">Quay lại</a></li>';
            }

            $begin = $page - 3;
    if ($begin < 1) {
        $begin = 1;
    }

    $end = $page + 3;
    if ($end > $maxPage) {
        $end = $maxPage;
    }

    for ($index = $begin; $index <= $end; $index++) {
        $active = $index == $page ? 'active' : false;
        echo '<li class="page-item"><a class="page-link '.$active.'" href="'.getPaginateUrl($index).'">'.$index.'</a></li>';
    }

    if ($page < $maxPage) {
        echo '<li class="page-item"><a class="page-link" href="'.getPaginateUrl($page+1).'">Tiếp theo</a></li>';
    }
    ?>

    </ul>
</nav>
<?php endif; ?>