<?php echo '<?xml '; ?>version ="1.0" encoding="utf-8"?>
<rows>
        <page><?php echo $data['page'];?></page>
        <total><?php echo $data['total_pages']; ?></total>
        <records><?php echo $data['total']; ?></records>
<?php foreach($data['objects'] as $obj): ?>
        <row>
                <cell><?php echo $obj['id']; ?></cell>
                <cell><?php echo $obj['name']; ?></cell>
                <cell><?php echo $obj['created_at']; ?></cell>
                <cell><?php echo $obj['updated_at']; ?></cell>
        </row>
<?php endforeach; ?>
</rows>
