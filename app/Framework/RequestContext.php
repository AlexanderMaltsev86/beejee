<?php


namespace App\Framework;


class RequestContext {
    /**
     * @var array
     */
    private $variables = [];

    public function setVariable(string $name, $value) {
        $this->variables[$name] = $value;
    }

    public function getVariable(string $name) {
        if (isset($this->variables[$name])) {
            return $this->variables[$name];
        } else {
            return null;
        }
    }

    /**
     * @return array
     */
    public function getVariables(): array {
        return $this->variables;
    }
}