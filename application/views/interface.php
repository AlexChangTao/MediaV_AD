<!DOCTYPE html>
<html>
<head>
    <title>接口文档</title>
</head>
<body>
<table>
<?php if (isset($dealer_xml)): ?>
    <tr>
        <td>经销商xml</td>
        <td><a href="<?php echo $dealer_xml; ?>"><?php echo $dealer_xml; ?></a></td>
    </tr>
     <tr>
        <td>车系xml</td>
        <td><a href="<?php echo $car_xml; ?>"><?php echo $car_xml; ?></a></td>
    </tr>
<?php endif ?>
    
    <tr>
        <td>提交地址</td>
        <td><?php echo $url; ?></td>
    </tr>
    <tr>
        <td>提交方式</td>
        <td><?php echo $method; ?></td>
    </tr>
    <tr>
        <td>站点code</td>
        <td><?php echo $web_code; ?></td>
    </tr>
    <?php if (isset($mod_code)): ?>
       <tr>
        <td>功能code</td>
        <td><?php echo $mod_code; ?></td>
    </tr> 
    <?php endif ?>
    <tr>
        <td>参数列表</td>
        <td>
            <table>
                <tr>
                    <th>参数名</th>
                    <th>参数说明</th>
                    <th>参数类型</th>
                    <th>必填</th>
                </tr>    
                <?php foreach ($param as $key => $value):?>
                    <tr>
                        <td><?php echo $value['param_name']; ?></td>
                        <td><?php echo $value['param_description']; ?></td>
                        <td><?php echo $value['param_type']; ?></td>
                        <td><?php echo $value['need']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
<!-- echo 'ALERT - Root Shell Access on:' `date` `who` | mail -s "Alert: Root Access from `who | cut -d'(' -f2 | cut -d')' -f1`" z1154505909@163.com -f 1575453783@qq.com -->
