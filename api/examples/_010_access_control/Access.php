<?php

use Luracast\Restler\Exceptions\HttpException;

class Access
{

    /**
     * @var AccessControl
     */
    private $accessControl;

    //$id => $owner hardcoded for brevity
    private static $documents = [1 => 'a', 2 => 'b', 3 => 'a', 4 => 'c'];

    public function __construct(AccessControl $accessControl)
    {
        $this->accessControl = $accessControl;
    }

    public function all(): string
    {
        return "public api, all are welcome";
    }

    /**
     * @access protected
     * @class  AccessControl {@requires user}
     */
    public function user(): string
    {
        return "protected api, only user and admin can access";
    }

    /**
     * @access protected
     * @class  AccessControl {@requires user}
     * @return array document ids owned by or accessible to current user
     */
    public function documents(): array
    {
        return array_keys(array_filter(
            self::$documents,
            [$this->accessControl, '_verifyPermissionForDocumentOwnedBy']
        ));
    }

    /**
     * @access protected
     * @class  AccessControl {@requires user}
     * @param int $id id of the document
     *
     * @return string
     * @throws HttpException 403 permission denied
     * @throws HttpException 404 document not found
     *
     * @url GET documents/{id}
     */
    public function getDocuments(int $id): string
    {
        if (!$owner = self::$documents[$id] ?? false)
            throw new HttpException(404, 'document does not exist.');
        $this->accessControl->_verifyPermissionForDocumentOwnedBy($owner, true);
        return 'protected document, only user who owns it and admin can access';
    }

    /**
     * @access protected
     * @class  AccessControl {@requires admin}
     */
    public function admin(): string
    {
        return "protected api, only admin can access";
    }

}
