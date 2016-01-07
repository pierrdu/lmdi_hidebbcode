<?php
/**
*
* @package phpBB Extension - LMDI extension de glossaire
* @copyright (c) 2015 LMID - Pierre Duhem
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace lmdi\hidebbcode\migrations;

class release_1_0_0 extends \phpbb\db\migration\migration
{
	public function update_schema()
	{
		return array(
			'add_columns'		=> array(
				$this->table_prefix . 'bbcodes'		=> array(
					'lmdi'	=> array('USINT', 0),
				),
			),
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function revert_schema()
	{
		return array(
			'drop_columns'		=> array(
				$this->table_prefix . 'bbcodes'		=> array(
					'lmdi',
				),
			),
		);
	}
}
