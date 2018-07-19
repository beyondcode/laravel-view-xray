<?php

namespace BeyondCode\ViewXray;

use View;

class Xray
{
    protected $baseView;

    protected $viewId = 0;

    public function boot()
    {
        View::composer('*', function ($view) {
            if (is_null($this->baseView)) {
                $this->baseView = clone $view;
            }

            if ($this->isEnabledForView($view->getName())) {
                $this->modifyView($view);
            }
        });
    }

    public function isEnabled()
    {
        return config('xray.enabled');
    }

    public function modifyView($view)
    {
        $viewContent = file_get_contents($view->getPath());

        $file = tempnam(sys_get_temp_dir(), $view->name());

        $re = '/(@section\(([^))]+)\)+)(.*?)(@endsection|@show|@overwrite)/s';
        $viewContent = preg_replace_callback($re, function ($matches) use ($view) {
            ++$this->viewId;
            $sectionName = str_replace(["'", '"'], '', $matches[2]);
            return $matches[1] . '<!--XRAY START ' . $this->viewId . ' ' . $view->getName() . '@section:' . $sectionName . ' ' . $view->getPath() . '-->' . $matches[3] . '<!--XRAY END ' . $this->viewId . '-->' . $matches[4];
        }, $viewContent);

        $viewContent = '<!--XRAY START ' . $this->viewId . ' ' . $view->getName() . ' ' . $view->getPath() . '-->' . PHP_EOL . $viewContent . PHP_EOL . '<!--XRAY END ' . $this->viewId . '-->';

        file_put_contents($file, $viewContent);

        $view->setPath($file);

        ++$this->viewId;
    }

    public function getBaseView()
    {
        return $this->baseView;
    }

    protected function isEnabledForView(string $viewName): bool
    {
        return ! in_array($viewName, config('xray.excluded', []));
    }

}
