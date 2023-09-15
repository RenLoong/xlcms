<?php

namespace app\admin\controller;

use app\BaseController;
use app\common\model\Article as ModelArticle;
use app\common\model\ArticleClassify;
use app\common\model\ArticleContent;
use app\common\validate\Article as ValidateArticle;
use think\Exception;
use think\facade\Db;
use think\Request;

class Article extends BaseController
{
    public function index(Request $request)
    {
        try {
            $id = $request->get('id');
            $ModelArticle = ModelArticle::where(['article.id' => $id])->alias('article')
                ->join('article_content content', 'content.article_id = article.id')
                ->field('article.*,content.content')->find();
            if ($ModelArticle) {
                return $this->success('success', $ModelArticle);
            } else {
                return $this->fail('文章不存在');
            }
        } catch (\Throwable $th) {
            return $this->exception($th);
        }
    }
    public function indexQuery(Request $request)
    {
        try {
            $G = $request->get();
            $per_page = 10;
            if (!empty($G['per_page'])) {
                $per_page = (int)$G['per_page'];
            }
            $where = [];
            if (!empty($G['title'])) {
                $where[] = ['article.title', 'like', '%' . $G['title'] . '%'];
            }
            if (!empty($G['cid'])) {
                $where[] = ['article.cid', '=', $G['cid']];
            }
            if (!empty($G['state'])) {
                $where[] = ['article.state', '=', $G['state']];
            }
            if (!empty($G['classify_alias'])) {
                $where[] = ['classify.alias', '=', $G['classify_alias']];
            }
            $field = [
                'article.*',
                'classify.title as classify_title',
            ];
            $Data = ModelArticle::alias('article')
                ->join('article_classify classify', 'classify.id=article.cid')
                ->where($where)
                ->field($field)
                ->paginate($per_page)->each(function ($item) {
                    $item->state_loading = false;
                });
            return $this->success('success', $Data);
        } catch (\Throwable $th) {
            return $this->exception($th);
        }
    }
    public function indexDelete(Request $request)
    {
        $id = $request->post('id');
        Db::startTrans();
        try {
            $Article = ModelArticle::where(['id' => $id])->find();
            if (!$Article) {
                throw new Exception('文章不存在');
            }
            $ArticleContent = ArticleContent::where(['article_id' => $Article->id])->find();
            if (!$ArticleContent) {
                throw new Exception('文章内容不存在');
            }
            $Article->delete();
            $ArticleContent->delete();
            Db::commit();
            return $this->success('删除成功');
        } catch (\Throwable $th) {
            Db::rollback();
            return $this->exception($th);
        }
    }
    public function indexAdd(Request $request)
    {
        $D = $request->post();
        $Validate = new ValidateArticle;
        if (!$Validate->check($D)) {
            return $this->fail($Validate->getError());
        }
        Db::startTrans();
        try {
            $Article = new ModelArticle;
            if (!empty($D['classify_alias'])) {
                $Article->cid = ArticleClassify::where(['alias' => $D['classify_alias']])->value('id');
                if (!$Article->cid) {
                    throw new Exception('分类不存在');
                }
            } else {
                $Article->cid = $D['cid'];
            }
            $Article->title = $D['title'];
            $Article->subtitle = $D['subtitle'];
            if ($D['alias']) {
                $Article->alias = $D['alias'];
            }
            $Article->state = $D['state'];
            if ((int)$D['view'] < 0) {
                $D['view'] = 0;
            }
            $Article->view = $D['view'];
            $Article->thumb = json_encode($D['thumb']);
            $Article->class = $D['class'];
            $Article->source = $D['source'];
            $Article->desc = $D['desc'];
            if ((int)$D['sort'] > 99 || (int)$D['sort'] < 0) {
                $D['sort'] = 99;
            }
            $Article->sort = $D['sort'];
            if ($D['start_time']) {
                $Article->start_time = $D['start_time'];
            }
            if ($D['end_time']) {
                $Article->end_time = $D['end_time'];
            }
            $Article->save();
            $ArticleContent = new ArticleContent;
            $ArticleContent->article_id = $Article->id;
            $ArticleContent->content = $D['content'];
            $ArticleContent->save();
            Db::commit();
            return $this->success('发布成功');
        } catch (\Throwable $th) {
            Db::rollback();
            return $this->exception($th);
        }
    }
    public function indexEdit(Request $request)
    {
        $D = $request->post();
        $Validate = new ValidateArticle;
        if (!$Validate->check($D)) {
            return $this->fail($Validate->getError());
        }
        Db::startTrans();
        try {
            $Article = ModelArticle::where(['id' => $D['id']])->find();
            if (!empty($D['classify_alias'])) {
                $Article->cid = ArticleClassify::where(['alias' => $D['classify_alias']])->value('id');
                if (!$Article->cid) {
                    throw new Exception('分类不存在');
                }
            } else {
                $Article->cid = $D['cid'];
            }
            $Article->title = $D['title'];
            $Article->subtitle = $D['subtitle'];
            if ($D['alias']) {
                $Article->alias = $D['alias'];
            } else {
                $Article->alias = null;
            }
            $Article->state = $D['state'];
            if ((int)$D['view'] < 0) {
                $D['view'] = 0;
            }
            $Article->view = $D['view'];
            $Article->thumb = json_encode($D['thumb']);
            $Article->class = $D['class'];
            $Article->source = $D['source'];
            $Article->desc = $D['desc'];
            if ((int)$D['sort'] > 99 || (int)$D['sort'] < 0) {
                $D['sort'] = 99;
            }
            $Article->sort = $D['sort'];
            if ($D['start_time']) {
                $Article->start_time = $D['start_time'];
            } else {
                $Article->start_time = null;
            }
            if ($D['end_time']) {
                $Article->end_time = $D['end_time'];
            } else {
                $Article->end_time = null;
            }
            $Article->save();
            $ArticleContent = ArticleContent::where(['article_id' => $Article->id])->find();
            $ArticleContent->content = $D['content'];
            $ArticleContent->save();
            Db::commit();
            return $this->success('发布成功');
        } catch (\Throwable $th) {
            Db::rollback();
            return $this->exception($th);
        }
    }
    public function indexRestore(Request $request)
    {
    }
    public function indexSetState(Request $request)
    {
        $id = $request->post('id');
        $Article = ModelArticle::where(['id' => $id])->find();
        if (!$Article) {
            return $this->fail('文章不存在');
        }
        $Article->state = $Article->state ? 0 : 1;
        if ($Article->save()) {
            return $this->success('状态已变更');
        } else {
            return $this->fail('设置失败');
        }
    }
    public function indexCache(Request $request)
    {
    }
    public function indexSetSort(Request $request)
    {
        $id = $request->post('id');
        $sort = $request->post('sort');
        $Article = ModelArticle::where(['id' => $id])->find();
        if (!$Article) {
            return $this->fail('文章不存在');
        }
        if ((int)$sort > 99 || (int)$sort < 0) {
            return $this->fail('排序值必须在0-99之间');
        }
        $Article->sort = $sort;
        if ($Article->save()) {
            return $this->success('排序已变更');
        } else {
            return $this->fail('设置失败');
        }
    }
    public function classify(Request $request)
    {
        try {
            $Data = ArticleClassify::getCate();
            return $this->success('success', $Data);
        } catch (\Throwable $th) {
            return $this->exception($th);
        }
    }
    public function classifyQuery(Request $request)
    {
        try {
            $G = $request->get();
            if (empty($G['id'])) {
                $Data = ArticleClassify::whereNull('pid')->order('sort')->select();
            } else {
                $Data = ArticleClassify::where(['pid' => $G['id']])->order('sort')->select();
            }
            foreach ($Data as $item) {
                $item->articleCount = ModelArticle::where(['cid' => $item->id])->count();
                $item->hasChildren = ArticleClassify::where(['pid' => $item->id])->count() ? 1 : 0;
            }
            return $this->success('success', $Data);
        } catch (\Throwable $th) {
            return $this->exception($th);
        }
    }
    public function classifyDelete(Request $request)
    {
        $id = $request->post('id');
        $ArticleClassify = ArticleClassify::where(['id' => $id])->find();
        if (!$ArticleClassify) {
            return $this->fail('分类不存在');
        }
        if (ArticleClassify::where(['pid' => $ArticleClassify->id])->count()) {
            return $this->fail('请先删除子分类');
        }
        if (ModelArticle::where(['cid' => $ArticleClassify->id])->count()) {
            return $this->fail('请先将分类下的文章移动到其他分类');
        }
        if (!$ArticleClassify->delete()) {
            return $this->fail('删除失败');
        }
        return $this->success('删除成功');
    }
    public function classifyAdd(Request $request)
    {
        try {
            $D = $request->post();
            if (empty($D['title'])) {
                throw new Exception('分类名称不能为空');
            }
            $ArticleClassify = new ArticleClassify;
            if (!empty($D['pid'])) {
                $ArticleClassify->pid = $D['pid'];
            }
            $ArticleClassify->title = $D['title'];
            $ArticleClassify->alias = $D['alias'];
            $ArticleClassify->state = $D['state'];
            $ArticleClassify->sort = $D['sort'];
            if ($ArticleClassify->save()) {
                return $this->success('创建成功');
            } else {
                return $this->fail('创建失败');
            }
        } catch (\Throwable $th) {
            return $this->exception($th);
        }
    }
    public function classifyEdit(Request $request)
    {
        try {
            $D = $request->post();
            if (empty($D['title'])) {
                throw new Exception('分类名称不能为空');
            }
            $ArticleClassify = ArticleClassify::where(['id' => $D['id']])->find();
            if (!empty($D['pid'])) {
                $ArticleClassify->pid = $D['pid'];
            }
            $ArticleClassify->title = $D['title'];
            $ArticleClassify->alias = $D['alias'];
            $ArticleClassify->state = $D['state'];
            $ArticleClassify->sort = $D['sort'];
            if ($ArticleClassify->save()) {
                return $this->success('创建成功');
            } else {
                return $this->fail('创建失败');
            }
        } catch (\Throwable $th) {
            return $this->exception($th);
        }
    }
    public function classifyRestore(Request $request)
    {
    }
    public function classifySetState(Request $request)
    {
        $id = $request->post('id');
        $ArticleClassify = ArticleClassify::where(['id' => $id])->find();
        if (!$ArticleClassify) {
            return $this->fail('分类不存在');
        }
        $ArticleClassify->state = $ArticleClassify->state ? 0 : 1;
        if ($ArticleClassify->save()) {
            return $this->success('状态已变更');
        } else {
            return $this->fail('设置失败');
        }
    }
    public function classifyCache(Request $request)
    {
    }
}
