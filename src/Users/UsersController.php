<?php
namespace Weleoka\Users;

/**
 * A controller for users and admin related events.
 *
 */
class UsersController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;

   /**
     * Initialize the controller.
     *
     * @return void
     */


	public function initialize() {
        $this->users = new \Weleoka\Users\User();
        $this->users->setDI($this->di);
   }
   
   
   
/**
 * Login for user.
 *
 * @param
 *
 * @return void
 */
	public function loginAction($destination = null) {
        $this->theme->setTitle("Logga in");
        if ($this->users->isAuthenticated()) {
        			$destination = '/toLogin';
					$this->users->AddFeedback('<i class="fa fa-square-o"></i> Du är redan inloggad. <a href="' . $this->url->create('') . '/users/logout' . $destination . '"> Logga ut</a> för att fortsätta.');
        } else {
        $form = $this->getLoginForm();
        $status = $form->check();
        if ($status === true) {
				// Restart session timeout timer.
 				$this->users->sessionTimeoutRestart();
				$this->users->AddFeedback('Du är nu inloggad.');
				$url = $this->url->create('');
				if ($destination == 'toForum') {
					$url .= '/forum';
					$this->response->redirect($url);
				}
        	   $this->response->redirect($url);
        } else if ( $status === false ){
        	   $this->users->AddFeedback('Fel användarnamn eller lösenord.');
        	   $url = $this->url->create('users/login');
        	   $this->response->redirect($url);
        }

        $this->views->add('users/login',[
           'content' => $form->getHTML() . '<i class="fa fa-square-o"></i><a href="' . $this->url->create('') . '/users/add"> Skapa</a> ny användare.',
        ], 'main');
      }
   }



/**
 * Generate user loginform.
 *
 * @param
 *
 * @return form
 */
   protected function getLoginForm() {
       // $di = $this;

        $form = $this->form->create([], [
            'acronym' => [
                'type'        => 'text',
                'label'       => 'Användarnamn',
                'required'    => true,
                'maxlength'   => 255,
                'validation'  => array(
                    'not_empty'
                )
            ],
            'password' => [
                'label'       => 'Lösenord',
                'type'        => 'password',
                'required'    => true,
                'validation'  => array(
                    'not_empty'
                ),
            ],
            'submit' => [
                'value'     => 'Logga in',
                'type'      => 'submit',
                'class'		 => 'bigButton',
                'callback'  => function ($form) {		//use ($di) {
           
                    if( $this->users->loginUser( $form->Value( 'acronym' ), $form->Value( 'password' ) ) ) {
                    		$this->users->AddFeedback( 'Du är nu inloggad.' );
                        return true;
                    } else {
                    		$this->users->AddFeedback('Felaktigt användarnamn eller lösenord.');
                    		return false;
                 	  }
                }
            ]
        ]);
        return $form;
   }
   
   
   
/*
 * logout Action.
 *
 */
	public function logoutAction($destination = null) {
		session_unset();
		
		$url = $this->url->create('');
		if ($destination == 'toLogin') {
			$url .= '/users/login';
		}
		$this->users->AddFeedback('Du är nu utloggad.');
      $this->response->redirect($url);
	}



/**
 * List user with id.
 *
 * @param int $id of user to display
 *
 * @return void
 */
	public function idAction($id = null)
	{
			$this->theme->setTitle("Se specifik användarinformation");

         $this->views->add('me/page', [
	    		'content' => $this->sidebarGen(),
       		],'sidebar');

			$one = $this->users->find($id);
			$admin = $this->users->isAdmin() ? 1 : null;
			$this->views->add('users/list-one', [
				'user' => $one,
				'title' => 'Visar information för: ',
				'admin' => $admin,
			], 'main');

			$userQuestions = $this->users->findUserQuestions($id);
			$userAnswers = $this->users->findUserAnswers($id);

			if (isset($userQuestions)) {
				if (count($userQuestions > 1)) {
					$this->views->add('comments/questions', [
						'questions' => $userQuestions,
						'title' => 'Visar användarens frågor: ',
					], 'main');
				} else {
					$this->views->add('comments/question', [
						'question' => $userQuestions,
						'title' => 'Visar användarens frågor: ',
					], 'main');
				}
			}

			if (isset($userAnswers)) {
				if (count($userAnswers >= 1)) {					
					$this->views->add('comments/answers', [
						'answers' 	=> $userAnswers,
						'title' 		=> 'Visar användarens svar på frågor: ',
						'cleanView' => true,
					], 'main');
				} 
			}
	}



/**
 	* List all users.
	*
	* @return void
	*/
	public function listAction()
	{
		$all = $this->users->findAll();

		//Here starts the rendering phase of the list action
		$this->theme->setTitle("Alla användare");
		
		$admin = $this->users->isAdmin() ? 1 : null;
		$this->views->add('users/list-all', [
			'users'		 => $all,
			'title' 		 => 'Lista över alla användare',
			'admin'		 => $admin,
		], 'main');

      $this->views->add('me/page', [
         'content' => $this->sidebarGen(),
      ],'sidebar');
	}



    /**
     * Add new user.
     *
     * @return void
     */
	public function addAction()
	{
				$form = $this->form;
				$form = $form->create([], [
					'acronym' => [
						'type'        => 'text',
						'label'       => 'Användarnamn: ',
						'required'    => true,
						'placeholder' => 'Användarnamn',
						'validation'  => ['not_empty'],
					],
					'password' => [
						'type'        => 'password',
						'label'       => 'Lösenord: ',
						'required'    => true,
						'placeholder' => 'Lösenord',
						'validation'  => ['not_empty'],
					],
					'name' => [
						'type'        => 'text',
						'label'       => 'Ditt namn: ',
						'required'    => true,
						'placeholder' => 'Namn',
						'validation'  => ['not_empty'],
					],
					'email' => [
						'type'        => 'text',
						'label'		  => 'Email address: ',
						'required'    => true,
						'placeholder' => 'Email',
						'validation'  => ['not_empty', 'email_adress'],
					],
					'submit' => [
						'type'      => 'submit',
						'callback'  => function($form) {

						$this->users->save([
                        'acronym'   => $form->Value('acronym'),
                        'password'  => crypt($form->Value('password')),
                        'name'      => $form->Value('name'),
                        'email'     => $form->Value('email'),
                        'created'   => $now,
                        'active'    => getTime(),
						]);

						return true;
					}
				],

			]);

			// Check the status of the form
			$status = $form->check();

			if ($status === true) {
         // What to do if the form was submitted?
				$this->users->AddFeedback('Den nya användaren är nu i användarlistan.');
         	$url = $this->url->create('');
			   $this->response->redirect($url);

			} else if ($status === false) {
      	// What to do when form could not be processed?
				$this->users->AddFeedback('Den nya användaren kunde inte skapas.');
				$url = $this->url->create('users/add');
			   $this->response->redirect($url);
			}

			//Here starts the rendering phase of the add action
			$this->theme->setTitle("Lägg till användare");

	      $this->views->add('kmom03/page1', [
	    		'content' => $this->sidebarGen(),
       		],'sidebar');

			$this->views->add('users/add', [
				'content' =>$form->getHTML(),
				'title' => '<h2>Skapa en ny användare</h2>',
			]);
	}



    /**
     * Edit user.
     *
     * @param int id of user.
     *
     * @return void
     */
    public function updateAction($id)
    {
        $form = $this->form;
        $user = $this->users->find($id);
        $form = $form->create([], [
            'acronym' => [
                'type'        => 'text',
                'label'       => 'Acronym',
                'required'    => true,
                'validation'  => ['not_empty'],
                'value' => $user->acronym,
            ],
            'name' => [
                'type'        => 'text',
                'label'       => 'Name:',
                'required'    => true,
                'validation'  => ['not_empty'],
                'value' => $user->name,
            ],
            'email' => [
                'type'        => 'text',
                'required'    => true,
                'validation'  => ['not_empty', 'email_adress'],
                'value' => $user->email,
            ],
            'submit' => [
                'type'      => 'submit',
                'callback'  => function($form) use ($user) {

            	 		$this->users->save([
                 			'id'        => $user->id,
                 			'acronym'   => $form->Value('acronym'),
                 			'email'     => $form->Value('email'),
                 			'name'      => $form->Value('name'),
                 			'updated'   => getTime(),
                 			'active'    => getTime(),
                		]);

            	 		return true;
            	}
            ],
        ]);

        // Check the status of the form
        $status = $form->check();

        if ($status === true) {
        	 	$this->users->AddFeedback('Användaren har uppdaterats.');
        	 	$url = $this->url->create('users/id/' . $user->id);
			 	$this->response->redirect($url);

        } else if ($status === false) {
				$this->users->AddFeedback('Användaren uppdaterades inte.');
				$url = $this->url->create('users/edit/' . $id);
			 	$this->response->redirect($url);
        }

			//Here starts the rendering phase of the update action
			$this->theme->setTitle("Uppdatera en användare");

         $this->views->add('kmom03/page1', [
	    		'content' => $this->sidebarGen(),
       		],'sidebar');

			$this->views->add('users/edit', [
				'content' =>$form->getHTML(),
				'title' => '<h2>Uppdatera en användare</h2>',
			]);
}



/**
 * Delete user permanently.
 *
 * @param integer $id of user to delete.
 *
 * @return void
 */
public function deleteAction($id = null)
{
    if (!isset($id)) {
        die("Missing id");
    }
 	 $user = $this->users->find($id);

    $res = $this->users->delete($id);

 	 $this->users->AddFeedback($user->acronym . ' är nu permanent borttagen.');

	 $this->listAction();
}



/**
 * Delete (soft) and restore user.
 *
 * @param integer $id of user to delete.
 *
 * @return void
 */
public function softDeleteAction($id = null)
{
    if (!isset($id)) {
        die("Missing id");
    }

    $now = gmdate('Y-m-d H:i:s');

    $user = $this->users->find($id);

        if (!isset($user->deleted)) {
				$user->deleted = $now;
				$user->active = null;
        		$user->save();
        		$this->users->AddFeedback($user->acronym . ' är nu i papperskorgen.');
      		$this->listAction();

     	  } else {
				$user->deleted = null;
				$user->active = $now;
				$user->save();
				$this->users->AddFeedback($user->acronym . ' är nu aterställd.');
 				$this->listAction();
     	  }
}



    /**
     * Make user active/inactive.
     *
     * @param integer $id of user to deactivate.
     *
     * @return void
     */
    public function changeStatusAction($id = null) {
        if (!isset($id)) {
            die("Missing id");
        }

        $now = gmdate('Y-m-d H:i:s');

        $user = $this->users->find($id);

     	  if (!isset($user->active)) {
				$user->active = $now;
        		$user->save();
        		$this->users->AddFeedback($user->acronym . ' är nu aktiverad.');
        		$this->listAction();
 		  } else {
				$user->active = null;
				$user->save();
				$this->users->AddFeedback($user->acronym . ' är nu avaktiverad.');
 				$this->listAction();
 		  }
   }



/**
 * List all active and not deleted users.
 *
 * @return void
 */
public function activeAction()
{
    $all = $this->users->query()
        ->where('active IS NOT NULL')
        ->andWhere('deleted is NULL')
        ->execute();

    $this->theme->setTitle("Aktiva användare");
    $this->views->add('users/list-all', [
        'users' => $all,
        'title' => "Lista över aktiva användare",
    ]);
    $this->views->add('kmom03/page1', [
	    'content' => $this->sidebarGen(),
       ],'sidebar');
}



/**
 * List all inactive and not deleted users.
 *
 * @return void
 */
public function inactiveAction()
{
    $all = $this->users->query()
        ->where('active is NULL')
        ->andWhere('deleted is NULL')
        ->execute();

    $this->theme->setTitle("Inaktiva användare");
    $this->views->add('users/list-all', [
        'users' => $all,
        'title' => "Lista över inaktiva användare",
    ],'main');

    $this->views->add('kmom03/page1', [
	    'content' => $this->sidebarGen(),
       ],'sidebar');
}



/**
 * List all soft deleted users.
 *
 * @return void
 */
public function deletedAction()
{
    $all = $this->users->query()
        ->where('deleted IS NOT NULL')
        ->execute();

    $this->theme->setTitle("Papperskorgen");
    $this->views->add('users/list-all', [
        'users' => $all,
        'title' => "Lista över papperskorgen",
    ]);
    $this->views->add('kmom03/page1', [
	    'content' => $this->sidebarGen(),
       ],'sidebar');
}



 /**
 * Generate sidebar content.
 *
 * @param
 *
 * @return sidebar
 */
	public function sidebarGen()
	{	
	  $url = $this->url->create('');
	  if ($this->users->isAdmin()) {
     		$sidebar = '<p><i class="fa fa-refresh"></i><a href="' . $url . '/setup"> Nolställ DB</a></p
                 		<p><i class="fa fa-plus">    </i> <a href="' . $url . '/users/add"> Ny Användare</a></p>
                 		<p><i class="fa fa-check-square-o"></i><a href="' . $url . '/users/active"> Aktiva användare</a></p>
                 		<p><i class="fa fa-square-o"></i><a href="' . $url . '/users/inactive"> Inaktiva användare</a></p>
                 		<p><i class="fa fa-trash-o"></i><a href="' . $url . '/users/deleted"> Papperskorgen</a></p>
                 		<p><i class="fa fa-list-ol"></i><a href="' . $url . '/users/list"> Alla</a></p>';
			return $sidebar;     
     }
	  return '<i class="fa fa-square-o"></i><a href="' . $url . '/users/login"> Logga in</a> Admin<br>för att hantera användare.';
	}
}