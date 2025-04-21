<?php
require_once "framework/Model.php";
class UserFormAccess extends Model 
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // Ajouter un accès
    public static function addAccess($userId, $formId, $accessType)
    {
        $sql = "INSERT INTO user_form_accesses (user, form, access_type) VALUES (:user_id, :form_id, :access_type)";
        self::execute($sql,[
            "user_id"=>$userId,
            "form_id"=>$formId,
            "access_type"=>$accessType
        ]);
    }

    // Récupérer les accès par utilisateur
    public function getAccessByUser($userId)
    {
        $sql = "SELECT * FROM user_form_accesses WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer les accès par formulaire
    public static function getAccessByForm_and_user($formId,$userId)
    {
        $sql = "SELECT * FROM user_form_accesses WHERE form = :form_id AND user = :user_Id";
        $query = self::execute($sql,[
            "form_id"=>$formId,
            "user_Id"=>$userId
        ]);
        return $query->fetchAll(PDO::FETCH_ASSOC)[0];
    }

    // Mettre à jour un accès
    public static function updateAccess($userId, $formId, $accessType)
    {
        $sql = "UPDATE user_form_accesses SET access_type = :access_type WHERE user = :user_id AND form = :form_id";
        
        return self::execute(
            $sql,[
                "access_type"=>$accessType,
                "user_id" => $userId,
                "form_id" =>$formId
            ]
        );
    }

    // Supprimer un accès
    public static function deleteAccess($userId, $formId)
    {
        $sql = "DELETE FROM user_form_accesses WHERE user = :user_id AND form = :form_id";
       
        self::execute($sql,[
            "user_id" =>$userId,
            "form_id" =>$formId
        ]);
    }
}
