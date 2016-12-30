<style>
.mp_thbg th{
    background-color: rgb(226, 226, 226);
}
    tbody{
        height:1027px;
    }
    .mp_thbg .pager{
        font-size: 12px;
    }

</style>
<h1>IP地址列表</h1>

<table class="wp-list-table widefat mp_thbg">
    <thead>
    <tr>
        <th>id</th>
        <th>用户名</th>
        <th>IP地址</th>
        <th>登录次数</th>
        <th>首次登录时间</th>
        <th>最后登录时间</th>
    </tr>
    </thead>
    <tbody></tbody>
    <tfoot>
    <tr>
        <th colspan="8">
            <span class="pager">
            </span>
        </th>
    </tr>
    </tfoot>
</table>
<script>
    function getLogList(page,limit)
    {
        var $tbodyObj =  jQuery('.mp_thbg tbody');
        $tbodyObj.html('');
        $tbodyObj.html('<tr><td><div class="Loading"><stroing>加载中...</stroing></td></tr>');
        jQuery.ajax({
            url:'<?php echo $admin_ajax_url; ?>',
            data:{action:'mpAjaxGetIpsList',page:page,limit:limit},
            dataType:'json',
            type:'post',
            success:function(data)
            {
                if(data.status)
                {
                    $tbodyObj.html('');
                    $tbodyObj.append(data.data.list);
                    jQuery('.pager').html('');
                    jQuery('.pager').append(data.data.pageHtml);
                }
            },
            error:function(info,obj)
            {
                alert(obj);
            }
        })

    }

    getLogList(1,10);

    jQuery(".pager").delegate(".page","click",function(){
        var limit = 10;
        var page = jQuery(this).attr('page');
        getLogList(page,limit);
    });


</script>