<form action="admin.php"
      method="POST">
    <input type="hidden" name="uid" value="<?php print $row['form_id']; ?>" />
    <label>
        Имя<br />
        <input name="name" value="<?php print $row['name']; ?>"/>
    </label><br />

    <label>
        E-mail:<br />
        <input name="email" value="<?php print $row['email']; ?>" type="email" />
    </label><br />

    <label>
        Дата рождения:<br />
        <input name="birthday" value="<?php print $row['birthday']; ?>" type="date" />
    </label><br />

    Пол:<br />
    <label><input type="radio"
                  name="gender" value="M" <?php if ($row['gender'] == 'M') {print 'checked';} ?>/>
        мужской
    </label>
    <label><input type="radio"
                  name="gender" value="F" <?php if ($row['gender'] == 'F') {print 'checked';} ?> />
        женский
    </label>
    <label><input type="radio"
                  name="gender" value="O" <?php if ($row['gender'] == 'O') {print 'checked';} ?> />
        другое
    </label>
    <br />

    Количество конечностей:<br />
    <label><input type="radio" <?php if ($row['limb_number'] == '0') {print 'checked';} ?>
                  name="limbs" value="0" />
        0
    </label>
    <label><input type="radio" <?php if ($row['limb_number'] == '1') {print 'checked';} ?>
                  name="limbs" value="1" />
        1
    </label>
    <label><input type="radio" <?php if ($row['limb_number'] == '2') {print 'checked';} ?>
                  name="limbs" value="2" />
        2
    </label>
    <label><input type="radio" <?php if ($row['limb_number'] == '3') {print 'checked';} ?>
                  name="limbs" value="3" />
        3
    </label>
    <label><input type="radio" <?php if ($row['limb_number'] == '4') {print 'checked';} ?>
                  name="limbs" value="4" />
        4
    </label>
    <label><input type="radio" <?php if ($row['limb_number'] == '5') {print 'checked';} ?>
                  name="limbs" value="5" />
        5+
    </label>
    <br />

    <label>
        Сверхспособности:
        <br />
        <select name="superpowers[]"
                multiple="multiple">
            <option value="0" <?php if ($superpowers[0]) {print 'selected';} ?>>Бессмертие</option>
            <option value="1" <?php if ($superpowers[1]) {print 'selected';} ?>>Прохождение сквозь стены</option>
            <option value="2" <?php if ($superpowers[2]) {print 'selected';} ?>>Левитация</option>
        </select>
    </label><br />

    <label>
        Биография:<br />
        <textarea name="biography"><?php print $row['biography']; ?></textarea>
    </label><br />

    <input id="submit" type="submit" value="Изменить" name="update" />
    <input id="submit" type="submit" value="Удалить" name="delete"/>
</form>
