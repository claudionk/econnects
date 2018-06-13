<thead>
<tr>
    <?php if((isset($grid['config']['grid_checkbox'])) && ($grid['config']['grid_checkbox'] === TRUE)): ?>
        <th></th>
    <?php  endif; ?>
    <?php if(isset($grid['columns']) && is_array($grid['columns'])): ?>
        <?php foreach ($grid['columns'] as $column): ?>
            <th><?php echo (isset($column['label'])) ? $column['label'] : '-'?></th>
        <?php endforeach; ?>
    <?php endif; ?>
    <?php if(isset($grid['actions']) && is_array($grid['actions']) && count($grid['actions']) > 0): ?>
        <th>Ações</th>
    <?php endif; ?>
</tr>
</thead>