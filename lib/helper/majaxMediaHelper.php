<?php
function majaxMedia($reference)
{
  if ($reference instanceof sfOutputEscaper)
  {
    $ref = $reference->getRawValue();
  } else {
    $ref = $reference;
  }
  if ($ref instanceof majaxMediaRegistryEntry)
  {
    return new majaxMediaWrapperObject($ref);
  }
  if ($ref instanceof majaxMediaFileInfo)
  {
    return new majaxMediaWrapperFileInfo($ref);
  }
  throw new IllegalArgumentException('Reference was not a majaxMediaRegistryEntry or majaxMediaFileInfo Object');
  if (file_exists($ref))
  {
    return $ref;
  }
}
