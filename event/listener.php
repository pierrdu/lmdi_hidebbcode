<?php
/**
*
* @package phpBB Extension - LMDI Hide BB Code
* @copyright (c) 2015-2021 LMDI - Pierre Duhem
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
* L'événement se tropuve dans includes/functions_display.php
*/
class listener implements EventSubscriberInterface
{

	static public function getSubscribedEvents ()
	{
	return array(
		'core.display_custom_bbcodes_modify_sql'	=> 'hide_bbcode',
	);
	}

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\auth\auth */
	protected $auth;
	protected $db;

	/**
	* Constructor
	*
	* @param \phpbb\controller\helper	$helper	Controller helper object
	* @param \phpbb\template			$template	Template object
	*/
	public function __construct(\phpbb\user $user,
		\phpbb\auth\auth $auth,
		\phpbb\db\driver\driver_interface $db
		)
	{
		$this->user = $user;
		$this->auth = $auth;
		$this->db = $db;
	}

	public function hide_bbcode ($event)
	{
		$fid = request_var ('f', 0);
		// No forum id...
		if (!$fid)
		{
			$tid = request_var ('t', 0);
			if ($tid)
			{
				$sql = 'SELECT forum_id
				FROM ' . TOPICS_TABLE . "
				WHERE topic_id = $tid";
				$result = $this->db->sql_query($sql);
				$fid = (int) $this->db->sql_fetchfield('forum_id');
				$this->db->sql_freeresult($result);
			}
			else		// No topic id...
			{
				$pid = request_var ('p', 0);
				$sql = "SELECT forum_id FROM " . POSTS_TABLE . " WHERE post_id = $pid";
				$result = $this->db->sql_query($sql);
				$fid = (int) $this->db->sql_fetchfield('forum_id');
				$this->db->sql_freeresult($result);
			}
		}
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

