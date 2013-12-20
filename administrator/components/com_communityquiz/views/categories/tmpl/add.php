<?php
defined('_JEXEC') or die('Restricted access');
?>
<form name="adminForm" id="adminForm" action="index.php?option=<?php echo Q_APP_NAME;?>" method="post">
    <fieldset>
        <legend>Add Category</legend>
        <table class="admintable">
            <tr>
                <td width="100" align="right" class="key">
                    <label for="title"><?php echo JText::_( 'LBL_TITLE' ); ?>:</label>
                </td>
                <td>
                    <input size="40" type="text" id="title" name="title" value="<?php echo !empty($this->category->title)?$this->category->title:'';?>">
                </td>
            </tr>
            <tr>
                <td width="100" align="right" class="key">
                    <label for="category"><?php echo JText::_( 'LBL_PARENT_CATEGORY' ); ?>:</label>
                </td>
                <td>
                    <select name="category" id="category">
                    <?php
                    foreach($this->categories as $category){
                    ?>
                        <?php if(!empty($category->title)):?>
                        <option value="<?php echo $category->id;?>" <?php echo (isset($this->category) && $this->category->parent_id == $category->id)?'selected':'';?>>
                            <?php echo str_repeat('.', ($category->nlevel ? $category->nlevel-1 : 0) * 4) . $category->title;?>
                        </option>
                        <?php endif;?>
                    <?php
                    }
                    ?>
                    </select>
                </td>
            </tr>
        </table>
    </fieldset>
    <input type="hidden" name="id" value="<?php echo isset($this->category) ? $this->category->id : '';?>">
    <input type="hidden" name="view" value="categories">
    <input type="hidden" name="task" value="add">
</form>