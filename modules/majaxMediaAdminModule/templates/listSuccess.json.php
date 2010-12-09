<?php
$arr = array(
  'status' => 'success',
  'results' => array(
    'page'=> $data['page'],
    'total' => $data['total_pages'],
    'records' => $data['total'],
    'data' => array(),
  )
);
foreach($data['objects'] as $idx => $obj)
{
  $arr['results']['data'][] = array(
    'id' => $obj['id'],
    'name' => $obj['name'],
    'type' => $obj['type'],
    'created_at' => $obj['created_at'],
    'updated_at' => $obj['updated_at']
  );
}
echo json_encode($arr);
