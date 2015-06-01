<?php $count = 0; ?>

<?php foreach (SpaceMembership::GetUserSpaces() as $space): ?>
  <li class="visible-md visible-lg <?php if (isset(Yii::app()->params['currentSpace']) && Yii::app()->params['currentSpace'] != null && Yii::app()->params['currentSpace']->guid == $space->guid) { echo "active"; } ?>">
    <a href="<?php echo $space->getUrl(); ?>">
      <i class="fa fa-comments"></i><br>
      <?php echo CHtml::encode($space->name); ?>
    </a>
  </li>
<?php endforeach; ?>

