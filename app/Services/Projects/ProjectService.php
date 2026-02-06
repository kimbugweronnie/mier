<?php

use App\Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Project;

class ProjectService
{
    // USED ONLY IF THERE IS A DEADLOCK RISK. Otherwise, simple update is enough.
    // $attempts = 3;
    // while ($attempts > 0) {
    // try {
    // DB::transaction(function() use ($productId) {
    // $product =
    // Product::lockForUpdate()->findOrFail($productId);
    // if ($product->stock < 1) {
    // throw new \Exception("Out of stock");
    // }
    // $product->decrement('stock');
    // Order::create([
    // 'product_id' => $product->id,
    // 'quantity' => 1,
    // 'amount' => $product->price
    // ]);
    // });
    // break; // success
    // } catch (QueryException $e) {
    // // Check if deadlock
    // if ($e->getCode() == '40001' ||
    // str_contains($e->getMessage(),'Deadlock')) {
    // $attempts--;
    // if ($attempts == 0) throw $e;
    // // Optional: random sleep to avoid immediate conflict
    // usleep(rand(50,200)*1000);
    // } else {
    // throw $e;
    // }
    // }
    // }
    // public function safeTransaction(callable $callback, $retries = 3)
    // {
    //     while ($retries > 0) {
    //         try {
    //             return DB::transaction($callback);
    //         } catch (\Illuminate\Database\QueryException $e) {
    //             if ($e->getCode() == '40001' ||
    //             str_contains($e->getMessage(), 'Deadlock')) {
    //                 $retries--;
    //                 if ($retries == 0) {
    //                     throw $e;
    //                 }
    //                 usleep(rand(50, 200) * 1000);
    //             } else {
    //                 throw $e;
    //             }
    //         }
    //     }
    // }

//     Usage in OrderService:
// $this->safeTransaction(function() use ($productId) {
// $product = Product::lockForUpdate()->findOrFail($productId);
// $product->decrement('stock');
// Order::create(['product_id'=>$productId,'quantity'=>1,'amount'=>$produ
// ct->price]);
// });


    public function createOrder(int $productId, int $quantity, array $orderData): Order
    {
        return DB::transaction(function () use ($productId, $quantity,
            $orderData) {
            // Lock the product row to prevent race conditions
            $product = Product::lockForUpdate()->findOrFail($productId);
            if ($product->stock < $quantity) {
                throw new \Exception("Insufficient stock for product
{$product->id}");
            }
            // Decrement stock atomically
            $product->decrement('stock', $quantity);
            // Create order
            $order = new Order($orderData);
            $order->product_id = $product->id;
            $order->quantity = $quantity;
            $order->amount = $product->price * $quantity;
            $order->save();
            // Optional: trigger after-commit actions
            DB::afterCommit(function () use ($order) {
                //event(new OrderCreated($order));
                // e.g., dispatch a job, send notification
                // dispatch(new NotifyUserOrderJob($order));
            });

            return $order;
        });
    }

    public function updateOrder(int $orderId, array $updateData): Order
    {
        return DB::transaction(function () use ($orderId, $updateData) {
            $order = Order::lockForUpdate()->findOrFail($orderId);
            $product = Product::lockForUpdate()->findOrFail($order->product_id);
            // Adjust stock if quantity changed
            if (isset($updateData['quantity'])) {
                $diff = $updateData['quantity'] - $order->quantity;
                if ($product->stock < $diff) {
                    throw new \Exception('Insufficient stock to
increase order quantity');
                }
                $product->decrement('stock', $diff);
                $order->quantity = $updateData['quantity'];
                $order->amount = $order->quantity * $product->price;
            }
            // Update other fields
            foreach ($updateData as $key => $value) {
                if ($key !== 'quantity') {
                    $order->$key = $value;
                }
            }
            $order->save();
            DB::afterCommit(function () {
                // e.g., logging
                // event(new OrderUpdated($order));
            });

            return $order;
        });
    }

    public function deleteOrder(int $orderId)
    {
        return DB::transaction(function () use ($orderId) {
            $order = Order::lockForUpdate()->findOrFail($orderId);
            $product =
            Product::lockForUpdate()->findOrFail($order->product_id);
            // Restore stock
            $product->increment('stock', $order->quantity);
            // Soft delete
            $order->delete();
        });
    }

    public function restoreOrder(int $orderId)
    {
        return DB::transaction(function () use ($orderId) {
            $order =
            Order::withTrashed()->lockForUpdate()->findOrFail($orderId);
            $product =
            Product::lockForUpdate()->findOrFail($order->product_id);
            if ($product->stock < $order->quantity) {
                throw new \Exception('Cannot restore order,
insufficient stock');
            }
            $product->decrement('stock', $order->quantity);
            $order->restore();
        });
    }

    public function bulkCreateOrders(array $ordersData)
    {
        return DB::transaction(function () use ($ordersData) {
            $createdOrders = [];
            foreach ($ordersData as $data) {
                $createdOrders[] = $this->createOrder(
                    $data['product_id'],
                    $data['quantity'],
                    $data
                );
            }

            return $createdOrders;
        });
    }

    public function create(array $data): Project
    {
        // validate + create project
    }

    public function update(Project $project, array $data): Project
    {
        // Pessimistic Locking (SELECT FOR UPDATE)

        DB::transaction(function () use ($projectId) {
            $project = Project::where('id', $projectId)
                ->lockForUpdate()
                ->first();
            if ($project->stock <= 0) {
                throw new Exception('Out of stock');
            }
            $project->decrement('stock');
        });

        // Optimistic Locking (Alternative)
        $version =
        $updated = Project::where('id', $id)
            ->where('version', $version)
            ->update([
                'stock' => DB::raw('stock-1'),
                'version' => $version + 1,
            ]);
        if (! $updated) {
            throw new ConflictException;
        }

    }

    public function find(int $id): Project
    {
        Project::where('id', $id)
            ->sharedLock()
            ->first();
    }

    public function delete(Project $project): void
    {
        // delete project
    }
}
