<?php

namespace Stax;

if ( isset( $quote ) && $quote ) {
	echo wp_kses_post( $quote );
}
