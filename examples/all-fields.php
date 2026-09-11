<?php

require_once('../FormHelper.php');
$form = new FormHelper();

$form->setDoPassedStringCleanup(false);

// get information passed from the form, values will be defaults in the
// 		form when the page reloads
$first_name = $form->getPassed('first_name');
$favorite_color = $form->getPassed('favorite_color');
$favorite_number = $form->getPassed('favorite_number');
$secret_code = $form->getPassed('secret_code');
$form_rating = $form->getPassed('form_rating');
$email_address = $form->getPassed('email_address');
$phone_number = $form->getPassed('phone_number');
$next_birthday = $form->getPassed('next_birthday');
$hobby_movies = $form->getPassed('hobby_movies');
$hobby_sports = $form->getPassed('hobby_sports');
$hobby_books = $form->getPassed('hobby_books');
$movie  = $form->getPassed('movie');
$comments  = $form->getPassed('comments');
$city  = $form->getPassed('city');
$state  = $form->getPassed('state');
$show  = $form->getPassed('show');
$form_start_time = date('m/d/Y h:i:s A');
$food = $form->getPassed('food');
$form->setDoAddIdAttributeFromName(true);

?><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HTML Form Example - All Field Types</title>
    <link rel="stylesheet" href="./style.css?x=2">

    <!-- optional javacript -->
    <script>
    function updateFormRating() {
        var form_rating = document.getElementById('form_rating').value;
        document.getElementById('from_rating_display').innerHTML = form_rating;
    }

    function toggleInfo() {
        var elems = document.getElementsByClassName('formInfo');
        for (var i = 0; i < elems.length; i++) {
            elems[i].classList.toggle('isHidden');
        }
    }

    window.onload = function() {
        toggleInfo();
    };
    </script>

</head>

<body>
    <h1>HTML Form Example - All Field Types</h1>
    <div><a href="./">&laquo; back to All Examples</a></div><br><br>
    <?php

	// uncomment to view variables passed through the form
	/*if (!empty($_GET)) {
		echo '<br><br><br><div><b>Get Variables</b><pre>';
		var_dump($_GET);
		echo '</pre>';
	}*/

	$text = 'Show / Hide Info';
	$attributes = ['onclick' => 'toggleInfo()'];
	$form->button($text, $attributes);
	?>
    <?php $form->formStart(null, 'get', ['name' => 'myform']); ?>


    <div class="inputContainer">
        <label>Form Start Time (hidden field)</label>
        <?php $form->hidden('form_start_time', $form_start_time); ?>
        <ul class="formInfo">
            <li>&lt;input type="hidden"&gt;</li>
        </ul>
    </div>

    <div class="inputContainer">
        <label>First Name</label>
        <?php $form->text('first_name', $first_name); ?>
        <ul class="formInfo">
            <li>&lt;input type="text"&gt;</li>
        </ul>
    </div>

    <div class="inputContainer">
        <label>Favorite Color</label>
        <?php $form->color('favorite_color', $favorite_color); ?>
        <ul class="formInfo">
            <li>&lt;input type="color"&gt;</li>
        </ul>
    </div>

    <div class="inputContainer">
        <label>Favorite Number From 1 To 10</label>
        <?php
		$attributes = ['min' => 1, 'max' => 10];
		$form->number('favorite_number', $favorite_number, $attributes);
		?>
        <ul class="formInfo">
            <li>&lt;input type="number"&gt;</li>
        </ul>
    </div>

    <div class="inputContainer">
        <label>Rate this survey on a scale from 1 to 10</label>
        <div>Your Rating =
            <b><span id="from_rating_display"><?php echo $form_rating; ?></span></b>
        </div>
        <?php
		$attributes = [
			'onchange' => 'updateFormRating()',
			'id' => 'form_rating'
		];
		$form->range('form_rating', 1, 10, $form_rating, $attributes);
		?>
        <ul class="formInfo">
            <li>&lt;input type="range"&gt;</li>
            <li>"onclick" and "id" attributes were set to display the
                value using javascript</li>
        </ul>
    </div>

    <div class="inputContainer">
        <label>Email Address</label>
        <?php $form->email('email_address', $email_address); ?>
        <ul class="formInfo">
            <li>&lt;input type="email"&gt;</li>
        </ul>
    </div>

    <div class="inputContainer">
        <label>Phone Number</label>
        <?php $form->tel('phone_number', $phone_number); ?>
        <ul class="formInfo">
            <li>&lt;input type="tel"&gt;</li>
        </ul>
    </div>

    <div class="inputContainer">
        <label>Next Birthday Date</label>
        <?php $form->date('next_birthday', $next_birthday); ?>
        <ul class="formInfo">
            <li>&lt;input type="date"&gt;</li>
        </ul>
    </div>

    <div class="inputContainer">
        <label>Secret Code</label>
        <?php $form->password('secret_code'); ?>
        <ul class="formInfo">
            <li>&lt;input type="password"&gt;</li>
        </ul>
    </div>

    <div class="inputContainer">
        <label>Your Hobbies</label>
        <ul class="inputList">
            <li>
                <?php $form->checkbox('hobby_movies', $hobby_movies, 1, ['id' => 'hobby_movies']); ?>
                <label for="hobby_movies">Watching Movies</label>
            </li>
            <li>
                <?php $form->checkbox('hobby_sports', $hobby_sports, 1, ['id' => 'hobby_sports']); ?>
                <label for="hobby_sports">Playing Sports</label>
            </li>
            <li>
                <?php $form->checkbox('hobby_books', $hobby_books, 1, ['id' => 'hobby_books']); ?>
                <label for="hobby_books">Reading Books</label>
            </li>
        </ul>
        <ul class="formInfo">
            <li>&lt;input type="checkbox"&gt;</li>
        </ul>
    </div>

    <div class="inputContainer">
        <label>Favorite Foods</label>
        <ul class="inputList">
            <?php
			$foods = [
				'hamburger' => 'Hamburger',
				'pizza' => 'Pizza',
				'taco' => 'Taco',
			];
			foreach ($foods as $key => $display) {
				$id = 'food_' . $key;
				$isChecked = (!empty($food[$key]));
				echo '<li>';
				$form->checkbox('food[' . $key . ']', $isChecked, 1, ['id' => $id]);
				echo '<label for="' . $id . '">' . $form->htmlEscape($display) . '</label>';
				echo '</li>';
			}
			?>
        </ul>
        <ul class="formInfo">
            <li>&lt;input type="checkbox"&gt;</li>
            <li>set by array</li>
        </ul>
    </div>

    <div class="inputContainer">
        <label>Your Favorite Movie</label>
        <ul class="inputList">
            <li>
                <?php $form->radio('movie', 'star_wars', $movie, ['id' => 'star_wars']); ?>
                <label for="star_wars">Star Wars</label>
            </li>
            <li>
                <?php $form->radio('movie', 'sound_of_music', $movie, ['id' => 'sound_of_music']); ?>
                <label for="sound_of_music">Sound Of Music</label>
            </li>
            <li>
                <?php $form->radio('movie', $movie, 1, ['id' => 'avatar']); ?>
                <label for="avatar">Avatar</label>
            </li>
        </ul>
        <ul class="formInfo">
            <li>&lt;input type="radio"&gt;</li>
        </ul>
    </div>

    <div class="inputContainer">
        <label>Favorite TV Show</label>
        <ul class="inputList">
            <?php
			$shows = [
				'cheers' => 'Cheers',
				'seinfeld' => 'Seinfeld',
				'simpsons' => 'The Simpsons',
			];
			foreach ($shows as $key => $display) {
				$id = 'show_' . $key;
				echo '<li>';
				$form->radio('show', $key, $show, ['id' => $id]);
				echo '<label for="' . $id . '">' . $form->htmlEscape($display) . '</label>';
				echo '</li>';
			}
			?>
        </ul>
        <ul class="formInfo">
            <li>&lt;input type="radio"&gt;</li>
            <li>set by array</li>
        </ul>
    </div>

    <div class="inputContainer">
        <label>Comments</label>
        <?php $form->textarea('comments', $comments, ['style' => 'height:80px;']); ?>
        <ul class="formInfo">
            <li>&lt;textarea&gt;</li>
        </ul>
    </div>

    <div class="inputContainer">
        <label>Favorite City</label>
        <?php
		$cities = [
			'' => '-- Select A City --',
			'California' => [
				'Los Angeles' => 'Los Angeles, CA',
				'San Diego' => 'San Diego, CA',
				'San Francisco' => 'San Francisco, CA'
			],
			'Texas' => [
				'Austin' => 'Austin, TX',
				'Houston' => 'Houston, TX'
			],
			'Boston' => 'Boston, MA',
			'New York' => 'New York, NY'
		];
		$form->select('city', $cities, $city);
		?>
        <ul class="formInfo">
            <li>&lt;select&gt;&lt;option&gt;&lt;/option&gt;&lt;/select&gt;</li>
            <li>&lt;select&gt;&lt;optgroup&gt;&lt;option&gt;&lt;/option&gt;&lt;/optgroup&gt;&lt;/select&gt;</li>
        </ul>
    </div>

    <div class="inputContainer">
        <label>Favorite State</label>
        <?php
		$states = [
			['id' => 34, 'abbreviation' => 'AZ', 'name' => 'Arizona', 'capital' => 'Phoenix'],
			['id' => 38, 'abbreviation' => 'CA', 'name' => 'California', 'capital' => 'Sacramento'],
			['id' => 49, 'abbreviation' => 'NY', 'name' => 'New York', 'capital' => 'Albany']
		];
		$form->selectByRecordSet('state', $states, 'id', 'name', '-- Select A City --', $state);
		?>
        <ul class="formInfo">
            <li>&lt;select&gt;&lt;option&gt;&lt;/option&gt;&lt;/select&gt;</li>
            <li>Set by database query results or a similar 2 dimensional array</li>
        </ul>
    </div>

    <div class="inputContainer">
        <?php $form->submit(); ?>
        <ul class="formInfo">
            <li>&lt;input type="submit"&gt;</li>
        </ul>
    </div>

    <div class="inputContainer">
        <?php $form->button(); ?>
        <ul class="formInfo">
            <li>&lt;button&gt;</li>
        </ul>
    </div>

    <div class="inputContainer">
        <?php $form->reset(); ?>
        <ul class="formInfo">
            <li>&lt;input type="reset"&gt;</li>
        </ul>
    </div>

    <?php $form->formEnd(); ?>
</body>

</html>