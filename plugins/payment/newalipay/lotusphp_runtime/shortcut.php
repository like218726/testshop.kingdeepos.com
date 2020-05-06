<?php
//原本SDK方法名为大写C  因为和框架冲突 改成CC
function CC($className)
{
	return LtObjectUtil::singleton($className);
}
