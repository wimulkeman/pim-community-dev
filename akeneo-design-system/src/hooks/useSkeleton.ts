import {useContext} from 'react';
import {SkeletonContext} from 'context/Skeleton';

const useSkeleton = () => {
  return useContext(SkeletonContext);
};

export {useSkeleton};
