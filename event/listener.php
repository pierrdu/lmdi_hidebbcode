<?php
/**
*
* @package phpBB Extension - LMDI Delete Re:
* @copyright (c) 2015 LMDI - Pierre Duhem
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace lmdi\hidebbcode\event;

/**
* @ignore
*/
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{

	static public function getSubscribedEvents ()
	{
	return array(
		// 'core.display_custom_bbcodes_modify_row'	=> 'hide_bbcode',
		'core.display_custom_bbcodes_modify_sql'	=> 'hide_bbcode',
	);
	}

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\auth\auth */
	protected $auth;

	/**
	* Constructor
	*
	* @param \phpbb\controller\helper	$helper	Controller helper object
	* @param \phpbb\template			$template	Template object
	*/
	public function __construct(\phpbb\user $user, \phpbb\auth\auth $auth)
	{
		$this->user = $user;
		$this->auth = $auth;
	}

	public function hide_bbcode ($event)
	{
		global $request;
		$fid = $request->variable ('f', 0);
		$sql_ary = $event['sql_ary'];
		$auto = 0;
		$autom  = $this->auth->acl_get('m_', $fid);
		$autoa  = $this->auth->acl_get('a_', $fid);
		if ($autom)
			$auto = 1;
		if ($autoa)
			$auto = 2;
		// var_dump ($auto);
		switch ($auto)
		{
			case 0 :
				$sql_ary['WHERE'] = "b.display_on_posting = 1 AND b.lmdi = 0";
				break;
			case 1 :
				$sql_ary['WHERE'] = "b.display_on_posting = 1 AND (b.lmdi = 0 OR b.lmdi = 1)";
				break;
			case 2 :
				$sql_ary['WHERE'] = "b.display_on_posting = 1 AND (b.lmdi = 0 OR b.lmdi = 2)";
				break;
		}
		$event['sql_ary'] = $sql_ary;
	}

}

