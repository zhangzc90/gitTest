<?php
interface TrafficTool{
	public function go();
} 
class Train implements TrafficTool{
	public function go(){
		echo 'travel by train';
	}
}
class Plane implements TrafficTool{
	public function go(){
		echo 'travel by plane';
	}
}
class Traveler{
	protected $trafficTool;
	public function __construct(TrafficTool $tool){
		$this->trafficTool=$tool;
	}
	public function travel(){
		$this->trafficTool->go();
	}
}
// 使用简单容器实现的依赖注入
class Container{
	protected $binds = [];
	protected $instances = [];

	/**
	 * 绑定：将回调函数绑定到字符指令上
	 *
	 * @param $abstract 字符指令，如 'train'
	 * @param $concrete 用于实例化组件的回调函数，如 function() { return new Train(); }
	 */
	public function bind($abstract, $concrete) {
		if ($concrete instanceof Closure) {
			// 向容器中添加可以执行的回调函数
			$this->binds[$abstract] = $concrete;
		} else {
			$this->instances[$abstract] = $concrete;
		}
	}

	/**
	 * 生产：执行回调函数
	 *
	 * @param $abstract     字符指令
	 * @param array $params 回调函数所需参数
	 * @return mixed        回调函数的返回值
	 */
	public function make($abstract, $params = []) {
		if (isset($this->instances[$abstract])) {
			return $this->instances[$abstract];
		}

		array_unshift($params, $this);

		// 将参数传递给回调函数
		return call_user_func_array($this->binds[$abstract], $params);
	}

}
print('<pre>');
$container=new Container();

$container->bind('traveler',function($container,$trafficTool){
	return new Traveler($container->make($trafficTool));
});

$container->bind('train',function($container){
	return new Train();
});

$me=$container->make('traveler',['train']);
$me->travel();