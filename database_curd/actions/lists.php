<?php 
    $user = get("SELECT user.*, groups.name AS group_name 
                FROM user 
                INNER JOIN groups ON user.group_id=groups.id 
                ORDER BY user.id DESC");

?>

<h2>Danh sách người dùng</h2>
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