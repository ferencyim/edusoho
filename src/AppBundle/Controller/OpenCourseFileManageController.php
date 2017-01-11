<?php

namespace AppBundle\Controller;

use Topxia\Common\Paginator;
use Topxia\Common\ArrayToolkit;
use Biz\File\Service\UploadFileService;
use Biz\OpenCourse\Service\OpenCourseService;
use Symfony\Component\HttpFoundation\Request;

class OpenCourseFileManageController extends BaseController
{
    public function indexAction(Request $request, $id)
    {
        $course = $this->getOpenCourseService()->tryManageOpenCourse($id);

        $conditions = array(
            'courseId' => $course['id'],
            'type'     => 'openCourse'
        );

        $paginator = new Paginator(
            $request,
            $this->getMaterialService()->searchMaterialCountGroupByFileId($conditions),
            20
        );

        //FIXME 同一个courseId下文件可能存在重复，所以需考虑去重，但没法直接根据groupbyFileId去重（sql_mode）
        // $materials = $this->getMaterialService()->searchMaterialsGroupByFileId(
        $materials = $this->getMaterialService()->searchMaterials(
            $conditions,
            array('createdTime' => 'DESC'),
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
        );

        $files      = $this->getMaterialService()->findFullFilesAndSort($materials);
        $fileIds    = ArrayToolkit::column($files, 'fileId');
        $filesQuote = $this->getMaterialService()->findUsedCourseMaterials($fileIds, $id);

        $users = $this->getUserService()->findUsersByIds(ArrayToolkit::column($files, 'updatedUserId'));

        return $this->render('TopxiaWebBundle:CourseFileManage:index.html.twig', array(
            'course'     => $course,
            'files'      => $files,
            'users'      => ArrayToolkit::index($users, 'id'),
            'paginator'  => $paginator,
            'now'        => time(),
            'filesQuote' => $filesQuote
        ));
    }

    public function showAction(Request $request, $id, $fileId)
    {
        $course = $this->getOpenCourseService()->tryManageOpenCourse($id);
        $file   = $this->getUploadFileService()->getFile($fileId);

        $materialCount = $this->getMaterialService()->searchMaterialCount(
            array(
                'courseId' => $id,
                'fileId'   => $fileId
            )
        );

        if (!$materialCount) {
            throw $this->createNotFoundException();
        }

        $file = $this->getUploadFileService()->getFile($fileId);

        if (empty($file)) {
            throw $this->createNotFoundException();
        }

        return $this->forward('AppBundle:UploadFile:download', array('fileId' => $file['id']));
    }

    public function convertAction(Request $request, $id, $fileId)
    {
        $course = $this->getOpenCourseService()->tryManageOpenCourse($id);

        $file = $this->getUploadFileService()->getFile($fileId);

        if (empty($file)) {
            throw $this->createNotFoundException();
        }

        $convertHash = $this->getUploadFileService()->reconvertFile($file['id']);

        if (empty($convertHash)) {
            return $this->createJsonResponse(array('status' => 'error', 'message' => $this->getServiceKernel()->trans('文件转换请求失败，请重试！')));
        }

        return $this->createJsonResponse(array('status' => 'ok'));
    }

    public function uploadCourseFilesAction(Request $request, $id, $targetType)
    {
        $course = $this->getOpenCourseService()->tryManageOpenCourse($id);

        return $this->render('TopxiaWebBundle:CourseFileManage:modal-upload-course-files.html.twig', array(
            'course'         => $course,
            'storageSetting' => $this->setting('storage', array()),
            'targetType'     => $targetType,
            'targetId'       => $id
        ));
    }

    public function batchUploadCourseFilesAction(Request $request, $id, $targetType)
    {
        if ("materiallib" != $targetType) {
            $course = $this->getOpenCourseService()->tryManageOpenCourse($id);
        } else {
            $course = null;
        }

        $fileExts = '';

        if ('opencourselesson' == $targetType) {
            $fileExts = "*.mp3;*.mp4;*.avi;*.flv;*.wmv;*.mov;*.mpg;*.ppt;*.pptx;*.doc;*.docx;*.pdf;*.swf";
        }

        return $this->render('TopxiaWebBundle:CourseFileManage:batch-upload.html.twig', array(
            'course'         => $course,
            'storageSetting' => $this->setting('storage', array()),
            'targetType'     => $targetType,
            'targetId'       => $id,
            'fileExts'       => $fileExts
        ));
    }

    public function deleteCourseFilesAction(Request $request, $id)
    {
        $course = $this->getOpenCourseService()->tryManageOpenCourse($id);

        if ($request->getMethod() == 'POST') {
            $formData = $request->request->all();

            $this->getMaterialService()->deleteMaterials($id, $formData['ids'], 'openCourse');

            if (isset($formData['isDeleteFile']) && $formData['isDeleteFile']) {
                foreach ($formData['ids'] as $key => $fileId) {
                    if ($this->getUploadFileService()->canManageFile($fileId)) {
                        $this->getUploadFileService()->deleteFile($fileId);
                    }
                }
            }

            return $this->createJsonResponse(true);
        }

        return $this->render('TopxiaWebBundle:CourseFileManage:file-delete-modal.html.twig', array(
            'course' => $course
        ));
    }

    public function deleteMaterialShowAction(Request $request, $id)
    {
        $course = $this->getOpenCourseService()->tryManageOpenCourse($id);

        $fileIds   = $request->request->get('ids');
        $materials = $this->getMaterialService()->findUsedCourseMaterials($fileIds, $id);
        $files     = $this->getUploadFileService()->findFilesByIds($fileIds, 0);
        $files     = ArrayToolkit::index($files, 'id');

        return $this->render('TopxiaWebBundle:CourseFileManage:file-delete-modal.html.twig', array(
            'course'    => $course,
            'materials' => $materials,
            'files'     => $files,
            'ids'       => $fileIds
        ));
    }

    public function lessonMaterialModalAction(Request $request, $courseId, $lessonId)
    {
        $course = $this->getOpenCourseService()->tryManageOpenCourse($courseId);
        $lesson = $this->getOpenCourseService()->getCourseLesson($courseId, $lessonId);

        $materials = $this->getMaterialService()->searchMaterials(
            array('lessonId' => $lesson['id'], 'type' => 'openCourse'),
            array('createdTime' => 'DESC'),
            0, 100
        );
        return $this->render('TopxiaWebBundle:CourseMaterialManage:material-modal.html.twig', array(
            'course'         => $course,
            'lesson'         => $lesson,
            'materials'      => $materials,
            'storageSetting' => $this->setting('storage'),
            'targetType'     => 'coursematerial',
            'targetId'       => $course['id']
        ));
    }

    /**
     * @return OpenCourseService
     */
    protected function getOpenCourseService()
    {
        return $this->getBiz()->service('OpenCourse:OpenCourseService');
    }

    /**
     * @return UploadFileService
     */
    protected function getUploadFileService()
    {
        return $this->getBiz()->service('File:UploadFileService');
    }

    protected function getMaterialService()
    {
        return $this->getBiz()->service('Course:MaterialService');
    }
}