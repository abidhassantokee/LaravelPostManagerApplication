<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\PostStoreRequest;
use App\Http\Requests\V1\PostUpdateRequest;
use App\Services\V1\PostService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PostController extends Controller
{
    /**
     * @var PostService
     */
    private $postService;

    /**
     * PostController constructor.
     */
    public function __construct()
    {
        $this->postService = new PostService();
    }

    /**
     * Returns list of post
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $sortBy = $request->input('sort_by') ? $request->input('sort_by') : 'created_at';
            $orderBy = strtolower($request->input('order_by')) === 'asc' ? 'asc' : 'desc';
            $view = (int)$request->input('view');
            return response()->json($this->postService->showAll($sortBy, $orderBy, $view));
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
     * Returns a post details
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            return response()->json($this->postService->show((int)$id));
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
     * Creates a post
     *
     * @param PostStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(PostStoreRequest $request)
    {
        try {
            return response()->json([
                'message' => 'Post created successfully.',
                'data' => $this->postService->store($request->validated())
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
     * Updates a post
     *
     * @param PostUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(PostUpdateRequest $request, $id)
    {
        try {
            return response()->json([
                'message' => 'Post updated successfully.',
                'data' => $this->postService->update($request->validated(), $id)
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
     * Deletes a post details
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $this->postService->destroy($id);
            return response()->json(['message' => 'Post deleted successfully.'], 200);
        } catch (HttpException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getStatusCode());
        } catch (\PDOException $e) {
            DB::rollBack();
            if (config('app.debug')) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'trace' => $e->getTrace()
                ], 500);
            }
            return response()->json(['message' => 'Oops! Something went wrong.'], 500);
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
