<?php if($data):foreach($data as $k => $v):?>
<tr>
    <td><?php echo $v['id']; ?></td>
    <td><?php echo $v['op_user_name']; ?></td>
    <td><?php echo $v['op_type']; ?></td>
    <td><?php echo $v['data_name']; ?></td>
    <td><?php echo $v['class']; ?></td>
    <td><?php echo $v['op_time']; ?></td>
    <td><?php echo $v['op_ip_id']; ?></td>
    <td><?php echo $v['other_info']; ?></td>
</tr>
<?php endforeach;endif;?>