<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\LikeStoreRequest;
use App\Services\V1\LikeService;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class LikeController extends Controller
{
    /**
     * @var LikeService
     */
    private $likeService;

    /**
     * LikeController constructor.
     */
    public function __construct()
    {
        $this->likeService = new LikeService();
    }

    /**
     * Returns like list by post id
     *
     * @param Request $request
     * @param $postId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLikesByPost(Request $request, $postId)
    {
        try {
            $sortBy = $request->input('sort_by') ? $request->input('sort_by') : 'created_at';
            $orderBy = strtolower($request->input('order_by')) === 'asc' ? 'asc' : 'desc';
            $view = (int)$request->input('view');
            return response()->json($this->likeService->getLikesByPost($postId, $sortBy, $orderBy, $view));
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
     * Creates a like on a post
     *
     * @param LikeStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(LikeStoreRequest $request)
    {
        try {
            return response()->json([
                'message' => 'Like created successfully.',
                'data' => $this->likeService->store($request->all())
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
     * Deletes a like
     *
     * @param $postId
     * @param $likeId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($postId, $likeId)
    {
        try {
            $this->likeService->destroy($postId, $likeId);
            return response()->json(['message' => 'Like deleted successfully.'], 200);
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
