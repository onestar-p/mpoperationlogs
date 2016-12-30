<?php if($data):foreach($data as $k => $v):?>
<tr>
    <td><?php echo $v['id']; ?></td>
    <td><?php echo $v['user_name']; ?></td>
    <td><?php echo $v['ip_address']; ?></td>
    <td><?php echo $v['record_nums']; ?></td>
    <td><?php echo $v['first_write_time']; ?></td>
    <td><?php echo $v['last_write_time']; ?></td>
</tr>
<?php endforeach;endif;?>