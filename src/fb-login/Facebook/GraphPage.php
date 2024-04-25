<?php





namespace Facebook;

/**
 * Class GraphPage.
 *
 * @author Artur Luiz <artur@arturluiz.com.br>
 */
class GraphPage extends GraphObject
{
    /**
     * Returns the ID for the user's page as a string if present.
     *
     * @return null|string
     */
    public function getId()
    {
        return $this->getProperty('id');
    }

    /**
     * Returns the Category for the user's page as a string if present.
     *
     * @return null|string
     */
    public function getCategory()
    {
        return $this->getProperty('category');
    }

    /**
     * Returns the Name of the user's page as a string if present.
     *
     * @return null|string
     */
    public function getName()
    {
        return $this->getProperty('name');
    }
}
