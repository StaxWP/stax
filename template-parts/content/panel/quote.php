<?php

namespace Stax;

if ( isset( $quote ) ) {
	echo wp_kses_post( $quote );
}

