import {
  GetStaticPaths,
  GetStaticProps,
  NextComponentType,
  NextPageContext,
} from "next";
import DefaultErrorPage from "next/error";
import Head from "next/head";
import { useRouter } from "next/router";
import { dehydrate, QueryClient, useQuery } from "react-query";

import { Form } from "../../../components/donation/Form";
import { PagedCollection } from "../../../types/collection";
import { Donation } from "../../../types/Donation";
import { fetch, FetchResponse, getPaths } from "../../../utils/dataAccess";

const getDonation = async (id: string | string[] | undefined) =>
  id ? await fetch<Donation>(`/donations/${id}`) : Promise.resolve(undefined);

const Page: NextComponentType<NextPageContext> = () => {
  const router = useRouter();
  const { id } = router.query;

  const { data: { data: donation } = {} } = useQuery<
    FetchResponse<Donation> | undefined
  >(["donation", id], () => getDonation(id));

  if (!donation) {
    return <DefaultErrorPage statusCode={404} />;
  }

  return (
    <div>
      <div>
        <Head>
          <title>{donation && `Edit Donation ${donation["@id"]}`}</title>
        </Head>
      </div>
      <Form donation={donation} />
    </div>
  );
};

export const getStaticProps: GetStaticProps = async ({
  params: { id } = {},
}) => {
  if (!id) throw new Error("id not in query param");
  const queryClient = new QueryClient();
  await queryClient.prefetchQuery(["donation", id], () => getDonation(id));

  return {
    props: {
      dehydratedState: dehydrate(queryClient),
    },
    revalidate: 1,
  };
};

export const getStaticPaths: GetStaticPaths = async () => {
  const response = await fetch<PagedCollection<Donation>>("/donations");
  const paths = await getPaths(response, "donations", "/donations/[id]/edit");

  return {
    paths,
    fallback: true,
  };
};

export default Page;
