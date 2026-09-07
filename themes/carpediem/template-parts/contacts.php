<?php defined( 'ABSPATH' ) || exit; ?>
<div class="contact-details">
	<?php if ( carpediem_setting( 'email' ) ) : ?><a href="mailto:<?php echo esc_attr( carpediem_setting( 'email' ) ); ?>"><?php echo esc_html( carpediem_setting( 'email' ) ); ?></a><?php endif; ?>
	<?php if ( carpediem_setting( 'phone' ) ) : ?><a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', carpediem_setting( 'phone' ) ) ); ?>"><?php echo esc_html( carpediem_setting( 'phone' ) ); ?></a><?php endif; ?>
	<?php if ( carpediem_setting( 'address' ) ) : ?><p><?php echo nl2br( esc_html( carpediem_setting( 'address' ) ) ); ?></p><?php endif; ?>
	<?php if ( carpediem_setting( 'hours' ) ) : ?><p><?php echo esc_html( carpediem_setting( 'hours' ) ); ?></p><?php endif; ?>
</div>
