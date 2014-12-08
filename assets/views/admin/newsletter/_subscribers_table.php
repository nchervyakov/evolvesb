<?php
$perRow = 4;
$subscrCount = count($subscribers);
$subscrRows = ceil($subscrCount / $perRow);
$selectedItems = isset($selectedSubscribers) && is_array($selectedSubscribers) ? $selectedSubscribers : [];
?>
<table class="table">
    <?php for ($si = 0; $si < $subscrRows; $si++): ?>
        <tr>
            <?php for ($sj = 0; $sj < $perRow; $sj++):
                $sIndex = $si * $perRow + $sj;
                ?>
                <td>
                    <?php if (isset($subscribers[$sIndex])):
                        $subscrItem = $subscribers[$sIndex];
                        ?>
                        <label>
                            <input type="checkbox" class="js-subscriber" value="<?php echo $subscrItem->id(); ?>" <?php
                                if (in_array($subscrItem->id(), $selectedItems)) { echo 'checked'; } ?>/>
                            <?php echo $subscrItem->email; ?>
                        </label>
                    <?php endif; ?>
                </td>
            <?php endfor; ?>
        </tr>
    <?php endfor; ?>
</table>