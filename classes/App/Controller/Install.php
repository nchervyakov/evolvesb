<?php
namespace App\Controller;

use App\Exception\NotFoundException;
use App\Page;

class Install extends Page {
    /**
     * show overview page
     */
    public function action_index() {

        // Check if the user can perform installation
        $isInstallAllowed = $this->pixie->config->get('parameters.allow_install');

        if (!$isInstallAllowed) {
            throw new NotFoundException();
        }

        $this->initView('installation');
        $this->view->headCSS = '<link href="/css/bootstrap.css" rel="stylesheet" type="text/css" media="all" />';
        $result = $this->pixie->installer->init($this->view)->runWizard($this->request);

        // If wizard successfully passed, install entered data.
        if ($result->isCompleted()) {
            $this->pixie->installer->finish();
            $this->pixie->session->set('isInstalled', true);
            $this->redirect('/');

        } else {
            // Current step (maybe not that we asked by the url, but one of invalid previous ones)
            $step = $result->getStep();

            if (!$step) {
                $step = $result->getLastStartedStep();
                $this->redirect('/install/' . $step->getName());
            }

            // Move next on successful step
            if ($step->getCompleted()) {
                $this->redirect('/install/' . $step->getNextStep()->getName());
                return;
            }

            // Redirect to invalid step if current step is greater than that step.
            if ($result->needRedirect()) {
                $this->redirect('/install/' . $step->getName());
                return;
            }

            $this->view->subview = $step->getTemplate();
            $this->view->errorMessage = implode('<br>', $step->getErrors());
            $this->view->step = $step;

            foreach ($result->getViewData() as $key => $value) {
                $this->view->$key = $value;
            }
            $this->view->bodyClass = "installation-page";
        }
    }
}