<?php


namespace App\Framework;

/**
 * View entity for MVC. Should be returned by controller if view needed.
 * @package App\Framework
 */
class View {
    /**
     * @var string
     */
    private $template;

    /**
     * @var array
     */
    private $params;

    /**
     * @var bool
     */
    private $useLayout;

    /**
     * Create a view. View will be rendered with passed template and params.
     * By default view uses layout (layout.phtml) but you can turn it off by passing $useLayout=false
     * @param string $template
     * @param array|null $params
     * @param bool $useLayout
     */
    public function __construct(string $template, ?array $params=null, bool $useLayout=true) {
        $this->template = $template;
        $this->params = $params;
        $this->useLayout = $useLayout;
    }

    /**
     * Render view. Should be called in the router if a view is returned by controller.
     */
    public function render() {
        if ($this->useLayout && file_exists(BASE_PATH . 'views/layout.phtml')) {
            $this->renderFile(BASE_PATH . 'views/layout.phtml');
        } else {
            $this->renderFile(BASE_PATH . 'views/' . $this->template . ".phtml");
        }
    }

    /**
     * Include another template inside current template. Should be called inside template.
     * Variables has own namespace, only passed variables will be available inside template.
     * @param string $template
     * @param $params
     */
    public function include(string $template, $params) {
        $view = new View($this->template, $params);
        $view->renderFile(BASE_PATH . 'views/' . $template . ".phtml");
    }

    /**
     * Render template content inside layout. Should be called in layout template.
     */
    public function renderContent() {
        $this->renderFile(BASE_PATH . 'views/' . $this->template . ".phtml");
    }

    private function renderFile(string $templateFile) {
        //Prepare local variables available in the view template
        foreach ($this->params as $key => $value) {
            ${$key} = $value;
        }
        $view = $this;
        $requestContext = DI::getInstance()->get('requestContext');
        require $templateFile;
    }

    /**
     * Get absolute URI path by relative path using basePath from config
     * @param string $relativePath
     * @return mixed|string
     */
    public function getPath(string $relativePath) {
        $di = DI::getInstance();
        $uriResolver = $di->get('uriResolver');
        return $uriResolver->getPath($relativePath);
    }

    /**
     * Get view template
     * @return string
     */
    public function getTemplate(): string {
        return $this->template;
    }

    /**
     * Get view params
     * @return array
     */
    public function getParams(): ?array {
        return $this->params;
    }

    /**
     * Get view parameter by key
     * @param string $paramKey
     * @return mixed|null
     */
    public function get(string $paramKey) {
        if (isset($this->params[$paramKey])) {
            return $this->params[$paramKey];
        } else {
            return null;
        }
    }
}