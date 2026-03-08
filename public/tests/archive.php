<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_role(['admin','doctor']);

$patient_id = $_GET['id'] ?? 0;

if(!$patient_id){
    header("Location:index.php");
    exit;
}

try {

    $pdo->beginTransaction();

    /*
    STEP 1 — GET ALL TESTS OF PATIENT
    */
    $stmt = $pdo->prepare("
        SELECT * 
        FROM patient_tests
        WHERE patient_id = ?
    ");
    $stmt->execute([$patient_id]);
    $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if(!empty($tests)){

        foreach($tests as $test){

            $test_id = $test['id'];

            /*
            STEP 2 — INSERT TEST INTO patient_tests_archive
            */
            $insertTestArchive = $pdo->prepare("
                INSERT INTO patient_tests_archive
                SELECT *
                FROM patient_tests
                WHERE id = ?
            ");
            $insertTestArchive->execute([$test_id]);

            /*
            STEP 3 — INSERT RELATED RESULTS INTO ARCHIVE
            */
            $insertResultsArchive = $pdo->prepare("
                INSERT INTO patient_test_results_archive
                SELECT *
                FROM patient_test_results
                WHERE patient_test_id = ?
            ");
            $insertResultsArchive->execute([$test_id]);

            /*
            STEP 4 — DELETE ORIGINAL RESULTS
            */
            $deleteResults = $pdo->prepare("
                DELETE FROM patient_test_results
                WHERE patient_test_id = ?
            ");
            $deleteResults->execute([$test_id]);

        }

        /*
        STEP 5 — DELETE ORIGINAL TEST RECORDS
        */
        $deleteTests = $pdo->prepare("
            DELETE FROM patient_tests
            WHERE patient_id = ?
        ");
        $deleteTests->execute([$patient_id]);
    }

    $pdo->commit();

} catch (Exception $e) {

    $pdo->rollBack();
    die("Archive failed: " . $e->getMessage());
}

header("Location:index.php");
exit;