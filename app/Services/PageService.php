<?php
namespace App\Services;

use App\Models\Page;
use App\Services\Utility\ImageService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PageService {

    private $options = "";

    public function store($data) {
        $data['position'] = $this->getMaxPosition($data['parent_id']);
        $images = [];
        if (isset($data['add_images'])) {
            $images = $data['add_images'];
            unset($data['add_images']);
        }
        $post = Page::firstOrCreate(['url' => $data['url']], $data);
        //если новости обрабатываем изображения
        if ($post && $post->parent_id == 2) {
                $this->newsImagesProcessing($images, $post->id);
        }
        cache()->flush();
    }

    private function newsImagesProcessing($files, $newsId)
    {
        $imageService = new ImageService();
        $i = 1;
        foreach ($files as $file) {
            if ($i === 1) {
                $savePath = public_path() . '/assets/images/posts/' . $newsId . '.webp' ;
            } else {
                $savePath = public_path() . '/assets/images/posts/' . $newsId . '/' . ($i - 1) . '.webp';
            }
            $imageService->createImageThumbnail($file->getRealPath(), $savePath, 800);
            $i++;
        }
    }
    public function update($data, $post) {
           $post->update($data);
           cache()->flush();
    }

    private function getMaxPosition($parent_id) {
        $position = 1;
        if ($parent_id !== 0) {
            $result = DB::table('pages')->select('position')->where('parent_id', $parent_id)
                ->whereNull('deleted_at')
                ->orderBy('position','desc')
                ->limit(1)
                ->get();
            if ($result->count()) {
                $position =  $result->toArray()[0]->position + 1;
            }
        }
        return $position;
    }

    public function getPagesTree()
    {
        $parentPages = Page::where('parent_id', 0)->orderBy('position')->get();
        $this->getPageChildren($parentPages);
        return $parentPages->toArray();
    }

    private function getPageChildren(&$parentPages, $level = 0)
    {
        foreach ($parentPages as $key => $parentPage) {
            $parentPages[$key]['level'] = $level;

            if ($parentPage['status'] === 0) {
                $parentPages[$key]['text'] = '<span class="no-active">' . $parentPage['name'] . '</span>';
            } else {
                $parentPages[$key]['text'] = $parentPage['name'];
            }
            $children = $parentPage->children;
            $childrenCount = $children->count();
            if ($childrenCount) {
                $level++;
                $parentPages[$key]['children'] = $this->getPageChildren($children, $level);
                $level--;
            }
        }
        if ($level !== 0) {
            $level--;
        }
    }

    public function getPageSelectOptions($selected = "", $page_id = false)
    {
        $pagesTree = $this->getPagesTree();
        $this->generateSelectOptions($pagesTree, $selected, $page_id);
        return $this->options;

    }

    private function generateSelectOptions($pagesTree, $selected, $page_id)
    {
        foreach($pagesTree as $pageTree) {
            if (!is_bool($page_id) && $page_id === $pageTree['id']) continue;
            $this->options .=
                '<option value="' . $pageTree['id'] . '" ' . (($pageTree['id'] == $selected) ? 'selected' : ''). '>' .
                str_repeat('&nbsp ', $pageTree['level']) . str_repeat('-', $pageTree['level']) . ' ' . $pageTree['name'] .
                '</option>';
            if ($pageTree['children']) {
                $this->generateSelectOptions($pageTree['children'], $selected, $page_id);
            }
        }
    }

    public function updatePagePosition($data)
    {
        $page = Page::find($data['id']);
        if ($page) {
            //исключить позицию если обновление странице не в текущей категории
            $this->excludePosition($page->parent_id, $page->position);
            $this->includePosition($page->id, $data['new_parent'], ++$data['new_position']);
            cache()->flush();
            return true;
        }
        return false;
    }

    private function excludePosition($parentId, $position)
    {
        DB::table("pages")->where('parent_id', $parentId)
            ->where('position', '>', $position)
            ->whereNull('deleted_at')
            ->decrement('position');

    }

    private function includePosition($pageId, $newParentId, $newPosition)
    {
        $newParentId = $newParentId ? $newParentId : 0;
        DB::table("pages")
            ->where('parent_id', $newParentId)
            ->where("position", ">=", $newPosition)
            ->whereNull('deleted_at')
            ->increment('position');
        DB::table("pages")
            ->where('id', $pageId)
            ->whereNull('deleted_at')
            ->update(['parent_id' => $newParentId, 'position' => $newPosition]);
    }



}
?>
