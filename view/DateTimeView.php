<?php

namespace View;

class DateTimeView
{


	public function show(): string
	{

		$timeString = date('l\, \t\h\e jS \o\f F Y\, \T\h\e \t\i\m\e \i\s H:i:s');

		return '<p>' . $timeString . '</p>';
	}
}
