<?php namespace NetzpStaging\Component;

class WorkerTask extends Task
{
	public function run() {
        $data = '';
        $task = $this->getTask();
        $cmd = $this->getCmd($task);

        if($cmd == 'start') {
            $this->setCurrentProfile();
            $this->setProfileStatus(self::PROFILE_STATUS_CREATING);
            $this->addTask($task);
            $this->setState(self::STATE_RUNNING);
            $this->setProgress(0, '');
            $data = 'start';
        }

        else if($cmd == 'abort') {
            $this->setProfileStatus(self::PROFILE_STATUS_ABORTED);
            $this->setState(self::STATE_ABORTED);
            $this->setCmdForTask($task, self::STATE_ABORT);
            $data = 'abort';
        }

        else if($cmd == 'results') {
            $data = json_encode($this->getResults());
        }

        else if($cmd == 'reset') {
            $this->getCache()->cacheDeleteAll();
            $data = 'reset';
        }

        else {
            $data = json_encode([
                'progress' => $this->getProgress(true), 
                'state' => $this->getState(true), 
                'profile' => $this->getProfileId(),
                'tasks' => $this->getTasks()
            ]);
        }

        return $data;
	}
}