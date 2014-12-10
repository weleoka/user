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
//            header('Location: ' . $this->url->create('users/login'));
        }
  		  $formOptions = [
            // 'start'           => false,  // Only return the start of the form element
            // 'columns' 	      => 1,      // Layout all elements in two columns
            // 'use_buttonbar'   => true,   // Layout consequtive buttons as one element wrapped in <p>
            // 'use_fieldset'    => true,   // Wrap form fields within <fieldset>
            // 'legend'          => isset($this->form['legend']) ? $this->form['legend'] : null,   // Use legend for fieldset
            // 'wrap_at_element' => false,  // Wraps column in equal size or at the set number of elements
        ];

        $this->views->add('users/login',[
           'content' => $form->getHTML($formOptions) . '<i class="fa fa-square-o"></i><a href="' . $this->url->create('') . '/users/add"> Skapa</a> ny användare.',
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
        $di = $this;

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
                'callback'  => function ($form) use ($di) {
                    if( $di->users->loginUser($form->Value('acronym'), $form->Value('password'))) {
                    		$this->users->AddFeedback('Du är nu inloggad.');
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
 * logout
 *
 */
	public function logoutAction($destination = null) {
		$this->session->un_set('user');
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
			$this->views->add('users/list-one', [
				'user' => $one,
				'title' => 'Visar information för: ',
			], 'main');
			
			$userQuestions = $this->users->findUserQuestions($id);
			$userAnswers = $this->users->findUserAnswers($id);
			
			if (isset($userQuestions)) {
			$this->views->add('comments/commentsqs', [
				'questions' => $userQuestions,
				'title' => 'Visar användarens frågor: ',
			], 'main');
			} if (isset($userAnswers)) {						
			$this->views->add('comments/commentsqs', [
				'questions' => $userAnswers,
				'title' => 'Visar användarens svar på frågor: ',
			], 'main');
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

		$this->views->add('users/list-all', [
			'users'		 => $all,
			'title' 		 => "Lista över alla användare",
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

						$now = gmdate('Y-m-d H:i:s');

						$this->users->save([
                        'acronym'   => $form->Value('acronym'),
                        'password'  => crypt($form->Value('password')),
                        'name'      => $form->Value('name'),
                        'email'     => $form->Value('email'),
                        'created'   => $now,
                        'active'    => $now,
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

			$formOptions = [
            // 'start'           => false,  // Only return the start of the form element
            // 'columns' 	      => 1,      // Layout all elements in two columns
            // 'use_buttonbar'   => true,   // Layout consequtive buttons as one element wrapped in <p>
            // 'use_fieldset'    => true,   // Wrap form fields within <fieldset>
            // 'legend'          => isset($this->form['legend']) ? $this->form['legend'] : null,   // Use legend for fieldset
            // 'wrap_at_element' => false,  // Wraps column in equal size or at the set number of elements
        	];

			$this->views->add('users/add', [
				'content' =>$form->getHTML($formOptions),
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

           	$now = gmdate('Y-m-d H:i:s');

            $this->users->save([
                 'id'        => $user->id,
                 'acronym'   => $form->Value('acronym'),
                 'email'     => $form->Value('email'),
                 'name'      => $form->Value('name'),
                 'updated'   => $now,
                 'active'    => $now,
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
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }

			//Here starts the rendering phase of the update action
			$this->theme->setTitle("Uppdatera en användare");

         $this->views->add('kmom03/page1', [
	    		'content' => $this->sidebarGen(),
       		],'sidebar');

			$formOptions = [
            // 'start'           => false,  // Only return the start of the form element
            // 'columns' 	         => 1,      // Layout all elements in two columns
            // 'use_buttonbar'   => true,   // Layout consequtive buttons as one element wrapped in <p>
            // 'use_fieldset'    => true,   // Wrap form fields within <fieldset>
            // 'legend'          => isset($this->form['legend']) ? $this->form['legend'] : null,   // Use legend for fieldset
            // 'wrap_at_element' => false,  // Wraps column in equal size or at the set number of elements
        	];

			$this->views->add('users/edit', [
				'content' =>$form->getHTML($formOptions),
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
    $this->views->add('users/list-short', [
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
    $this->views->add('users/list-short', [
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
    $this->views->add('users/list-short', [
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
     $sidebar = '<p><i class="fa fa-refresh"></i><a href="' . $url . '/setup"> Nolställ DB</a></p
                 <p><i class="fa fa-plus">    </i> <a href="' . $url . '/users/add"> Ny Användare</a></p>
                 <p><i class="fa fa-check-square-o"></i><a href="' . $url . '/users/active"> Aktiva användare</a></p>
                 <p><i class="fa fa-square-o"></i><a href="' . $url . '/users/inactive"> Inaktiva användare</a></p>
                 <p><i class="fa fa-trash-o"></i><a href="' . $url . '/users/deleted"> Papperskorgen</a></p>
                 <p><i class="fa fa-list-ol"></i><a href="' . $url . '/users/list"> Alla</a></p>';
	  return $sidebar;
	}
}