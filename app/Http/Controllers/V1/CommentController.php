<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\CommentStoreRequest;
use App\Services\V1\CommentService;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CommentController extends Controller
{
    /**
     * @var CommentService
     */
    private $commentService;

    /**
     * CommentController constructor.
     */
    public function __construct()
    {
        $this->commentService = new CommentService();
    }

    /**
     * Returns comment list by post id
     *
     * @param Request $request
     * @param $postId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCommentsByPost(Request $request, $postId)
    {
        try {
            $sortBy = $request->input('sort_by') ? $request->input('sort_by') : 'created_at';
            $orderBy = strtolower($request->input('order_by')) === 'asc' ? 'asc' : 'desc';
            $view = (int)$request->input('view');
            return response()->json($this->commentService->getCommentsByPost($postId, $sortBy, $orderBy, $view));
        } catch (HttpException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getStatusCode());
        } catch (\Exception $e) {
            if (config('app.debug')) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'trace' => $e->getTrace()
                ], 500);
            }
            return response()->json("Oops! Something went wrong.", 500);
        }
    }

    /**
     * Creates a comment on a post
     *
     * @param CommentStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CommentStoreRequest $request)
    {
        try {
            return response()->json([
                'message' => 'Comment created successfully.',
                'data' => $this->commentService->store($request->all())
            ], 201);
        } catch (HttpException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getStatusCode());
        } catch (\Exception $e) {
            if (config('app.debug')) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'trace' => $e->getTrace()
                ], 500);
            }
            return response()->json("Oops! Something went wrong.", 500);
        }
    }

    /**
     * Deletes a comment
     *
     * @param $postId
     * @param $commentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($postId, $commentId)
    {
        try {
            $this->commentService->destroy($postId, $commentId);
            return response()->json(['message' => 'Comment deleted successfully.'], 200);
        } catch (HttpException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getStatusCode());
        } catch (\Exception $e) {
            if (config('app.debug')) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'trace' => $e->getTrace()
                ], 500);
            }
            return response()->json("Oops! Something went wrong.", 500);
        }
    }
}
